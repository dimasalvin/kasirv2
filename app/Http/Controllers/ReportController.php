<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\CashRegister;
use App\Models\CashFlow;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // Laporan Penjualan
    public function sales(Request $request)
    {
        $tanggalDari = $request->get('tanggal_dari', Carbon::today()->toDateString());
        $tanggalSampai = $request->get('tanggal_sampai', Carbon::today()->toDateString());
        $shift = $request->get('shift');
        $metodeBayar = $request->get('metode_bayar');

        $query = Sale::with(['user', 'details.product'])
            ->where('status', 'completed')
            ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);

        if ($shift) {
            $query->where('shift', $shift);
        }

        if ($metodeBayar) {
            $query->where('metode_bayar', $metodeBayar);
        }

        $sales = $query->with('user')->orderBy('created_at', 'desc')->get();

        // Summary
        $totalPenjualan = $sales->sum('grand_total');
        $totalTunai = $sales->where('metode_bayar', 'tunai')->sum('grand_total');
        $totalNonTunai = $sales->where('metode_bayar', 'non_tunai')->sum('grand_total');
        $totalHV = $sales->where('tipe_penjualan', 'reguler')->sum('grand_total');
        $totalResep = $sales->where('tipe_penjualan', 'resep')->sum('grand_total');
        $jumlahTransaksi = $sales->count();

        return view('reports.sales', compact(
            'sales', 'tanggalDari', 'tanggalSampai', 'shift', 'metodeBayar',
            'totalPenjualan', 'totalTunai', 'totalNonTunai', 'totalHV', 'totalResep', 'jumlahTransaksi'
        ));
    }

    // Closing Kasir
    public function closingKasir(Request $request)
    {
        $registers = CashRegister::with('user')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reports.closing-kasir', compact('registers'));
    }

    public function openShift(Request $request)
    {
        $request->validate([
            'saldo_awal' => 'required|numeric|min:0',
            'shift' => 'required|in:pagi,siang',
        ]);

        // Cek apakah sudah ada shift yang open
        $existing = CashRegister::where('user_id', auth()->id())
            ->where('tanggal', Carbon::today())
            ->where('status', 'open')
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda masih memiliki shift yang belum ditutup.');
        }

        CashRegister::create([
            'tanggal' => Carbon::today(),
            'shift' => $request->shift,
            'user_id' => auth()->id(),
            'saldo_awal' => $request->saldo_awal,
            'status' => 'open',
        ]);

        return back()->with('success', 'Shift berhasil dibuka.');
    }

    // Tambah Pengeluaran saat shift open
    public function addExpense(Request $request, CashRegister $cashRegister)
    {
        if ($cashRegister->status === 'closed') {
            return back()->with('error', 'Shift sudah ditutup, tidak bisa menambah pengeluaran.');
        }

        $request->validate([
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255',
        ]);

        // Tambah ke total pengeluaran shift
        $cashRegister->increment('pengeluaran', $request->nominal);

        // Catat di cash flow
        CashFlow::create([
            'tanggal' => $cashRegister->tanggal,
            'tipe' => 'kredit',
            'nominal' => $request->nominal,
            'keterangan' => "Pengeluaran: {$request->keterangan}",
            'referensi' => "EXP-{$cashRegister->tanggal->format('Ymd')}-{$cashRegister->shift}",
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', "Pengeluaran Rp " . number_format($request->nominal, 0, ',', '.') . " berhasil dicatat.");
    }

    public function closeShift(CashRegister $cashRegister)
    {
        if ($cashRegister->status === 'closed') {
            return back()->with('error', 'Shift sudah ditutup.');
        }

        // Hitung total penjualan shift ini
        $sales = Sale::where('tanggal', $cashRegister->tanggal)
            ->where('shift', $cashRegister->shift)
            ->where('status', 'completed')
            ->get();

        $totalTunai = $sales->where('metode_bayar', 'tunai')->sum('grand_total');
        $totalNonTunai = $sales->where('metode_bayar', 'non_tunai')->sum('grand_total');
        $totalHV = $sales->where('tipe_penjualan', 'reguler')->sum('grand_total');
        $totalResep = $sales->where('tipe_penjualan', 'resep')->sum('grand_total');

        $saldoAkhir = $cashRegister->saldo_awal + $totalTunai - $cashRegister->pengeluaran;

        $cashRegister->update([
            'total_penjualan_tunai' => $totalTunai,
            'total_penjualan_non_tunai' => $totalNonTunai,
            'total_penjualan_hv' => $totalHV,
            'total_penjualan_resep' => $totalResep,
            'saldo_akhir' => $saldoAkhir,
            'status' => 'closed',
        ]);

        return back()->with('success', 'Shift berhasil ditutup.');
    }

    // Laporan Kas
    public function cashFlow(Request $request)
    {
        $tanggalDari = $request->get('tanggal_dari', Carbon::today()->startOfMonth()->toDateString());
        $tanggalSampai = $request->get('tanggal_sampai', Carbon::today()->toDateString());

        $cashFlows = CashFlow::with('user')
            ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalDebit = $cashFlows->where('tipe', 'debit')->sum('nominal');
        $totalKredit = $cashFlows->where('tipe', 'kredit')->sum('nominal');
        $saldo = $totalDebit - $totalKredit;

        return view('reports.cash-flow', compact(
            'cashFlows', 'tanggalDari', 'tanggalSampai',
            'totalDebit', 'totalKredit', 'saldo'
        ));
    }

    // Laporan Produk Terlaris
    public function topProducts(Request $request)
    {
        $tanggalDari = $request->get('tanggal_dari', Carbon::today()->startOfMonth()->toDateString());
        $tanggalSampai = $request->get('tanggal_sampai', Carbon::today()->toDateString());

        $topProducts = SaleDetail::join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.tanggal', [$tanggalDari, $tanggalSampai])
            ->select(
                'products.nama_barang',
                'products.kode_barang',
                DB::raw('SUM(sale_details.jumlah) as total_qty'),
                DB::raw('SUM(sale_details.subtotal) as total_penjualan')
            )
            ->groupBy('products.id', 'products.nama_barang', 'products.kode_barang')
            ->orderByDesc('total_qty')
            ->limit(20)
            ->get();

        return view('reports.top-products', compact('topProducts', 'tanggalDari', 'tanggalSampai'));
    }
}
