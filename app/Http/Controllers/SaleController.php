<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Models\Customer;
use App\Models\StockCard;
use App\Models\CashFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    // POS Kasir
    public function pos()
    {
        $products = Product::where('is_active', true)
            ->orderBy('nama_barang')
            ->get();

        $customers = Customer::orderBy('nama')->get();

        return view('pos.index', compact('products', 'customers'));
    }

    // Proses transaksi
    public function processTransaction(Request $request)
    {
        $request->validate([
            'items' => 'nullable|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.tipe_harga' => 'required|in:hv,resep',
            'items.*.diskon_persen' => 'nullable|numeric|min:0|max:100',
            'resep_groups' => 'nullable|array',
            'resep_groups.*.items' => 'required|array|min:1',
            'resep_groups.*.items.*.product_id' => 'required|exists:products,id',
            'resep_groups.*.items.*.jumlah' => 'required|integer|min:1',
            'resep_groups.*.pasien_nama' => 'required|string',
            'resep_groups.*.total' => 'required|numeric|min:0',
            'bayar' => 'required|numeric|min:0',
            'metode_bayar' => 'required|in:tunai,non_tunai',
            'tipe_penjualan' => 'required|in:reguler',
        ]);

        // Must have at least items or resep_groups
        if (empty($request->items) && empty($request->resep_groups)) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong.'], 422);
        }

        DB::beginTransaction();
        try {
            $noNota = Sale::generateNoNota();
            $now = Carbon::now();

            // Tentukan shift (2 shift: pagi 07:00-13:59, siang 14:00-21:00)
            $hour = $now->hour;
            $shift = ($hour >= 7 && $hour < 14) ? 'pagi' : 'siang';

            // Collect pasien data from first resep group (if any)
            $resepGroups = $request->resep_groups ?? [];
            $pasienNama = !empty($resepGroups) ? ($resepGroups[0]['pasien_nama'] ?? null) : null;
            $pasienNoHp = !empty($resepGroups) ? ($resepGroups[0]['pasien_no_hp'] ?? null) : null;
            $pasienAlamat = !empty($resepGroups) ? ($resepGroups[0]['pasien_alamat'] ?? null) : null;

            $sale = Sale::create([
                'no_nota' => $noNota,
                'tanggal' => $now->toDateString(),
                'jam' => $now->toTimeString(),
                'customer_id' => $request->customer_id,
                'user_id' => auth()->id(),
                'tipe_penjualan' => 'reguler',
                'shift' => $shift,
                'metode_bayar' => $request->metode_bayar,
                'referensi_bayar' => $request->referensi_bayar,
                'status' => 'completed',
                'catatan' => $request->catatan,
                'nama_dokter' => $request->nama_dokter,
                'pasien_nama' => $pasienNama ?? $request->nama_pelanggan,
                'pasien_no_hp' => $pasienNoHp,
                'pasien_alamat' => $pasienAlamat,
            ]);

            $subtotal = 0;
            $diskonTotal = 0;
            $hasStokMinus = false;

            // Process regular HV items
            foreach ($request->items ?? [] as $item) {
                $product = Product::find($item['product_id']);

                // Track stok minus
                if ($product->stok < $item['jumlah']) {
                    $hasStokMinus = true;
                }

                $hargaSatuan = $product->harga_hv;
                $diskonPersen = $item['diskon_persen'] ?? 0;
                $diskonNominal = ($hargaSatuan * $item['jumlah']) * ($diskonPersen / 100);
                $itemSubtotal = ($hargaSatuan * $item['jumlah']) - $diskonNominal;

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $hargaSatuan,
                    'diskon_persen' => $diskonPersen,
                    'diskon_nominal' => $diskonNominal,
                    'subtotal' => $itemSubtotal,
                    'tipe_harga' => 'hv',
                ]);

                // Kurangi stok
                $stokSebelum = $product->stok;
                $product->decrement('stok', $item['jumlah']);

                StockCard::create([
                    'product_id' => $product->id,
                    'tipe' => 'keluar',
                    'jumlah' => $item['jumlah'],
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSebelum - $item['jumlah'],
                    'referensi' => $noNota,
                    'keterangan' => 'Penjualan',
                    'user_id' => auth()->id(),
                ]);

                $subtotal += ($hargaSatuan * $item['jumlah']);
                $diskonTotal += $diskonNominal;
            }

            // Process resep groups — each group becomes individual product deductions
            foreach ($resepGroups as $group) {
                $resepTotal = 0;
                $groupDiskonPersen = $group['diskon_persen'] ?? 0;

                foreach ($group['items'] as $resepItem) {
                    $product = Product::find($resepItem['product_id']);

                    // Track stok minus
                    if ($product->stok < $resepItem['jumlah']) {
                        $hasStokMinus = true;
                    }

                    $hargaSatuan = $resepItem['harga_satuan'] ?? $product->harga_resep;
                    $itemGross = $hargaSatuan * $resepItem['jumlah'];
                    $itemDiskonNominal = $itemGross * ($groupDiskonPersen / 100);
                    $itemSubtotal = $itemGross - $itemDiskonNominal;

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $resepItem['product_id'],
                        'jumlah' => $resepItem['jumlah'],
                        'harga_satuan' => $hargaSatuan,
                        'diskon_persen' => $groupDiskonPersen,
                        'diskon_nominal' => $itemDiskonNominal,
                        'subtotal' => $itemSubtotal,
                        'tipe_harga' => 'resep',
                    ]);

                    // Kurangi stok
                    $stokSebelum = $product->stok;
                    $product->decrement('stok', $resepItem['jumlah']);

                    StockCard::create([
                        'product_id' => $product->id,
                        'tipe' => 'keluar',
                        'jumlah' => $resepItem['jumlah'],
                        'stok_sebelum' => $stokSebelum,
                        'stok_sesudah' => $stokSebelum - $resepItem['jumlah'],
                        'referensi' => $noNota,
                        'keterangan' => 'Penjualan Resep - ' . ($group['pasien_nama'] ?? ''),
                        'user_id' => auth()->id(),
                    ]);

                    $resepTotal += $itemGross;
                    $diskonTotal += $itemDiskonNominal;
                }

                $subtotal += $resepTotal;
            }

            $grandTotal = $subtotal - $diskonTotal;
            $kembalian = $request->bayar - $grandTotal;

            if ($request->metode_bayar === 'tunai' && round($request->bayar) < round($grandTotal)) {
                throw new \Exception('Pembayaran kurang dari total belanja.');
            }

            $sale->update([
                'subtotal' => round($subtotal),
                'diskon_total' => round($diskonTotal),
                'grand_total' => round($grandTotal),
                'bayar' => round($request->bayar),
                'kembalian' => max(0, round($kembalian)),
                'has_stok_minus' => $hasStokMinus,
            ]);

            // Catat cash flow
            CashFlow::create([
                'tanggal' => $now->toDateString(),
                'tipe' => 'debit',
                'nominal' => $grandTotal,
                'keterangan' => "Penjualan {$noNota}",
                'referensi' => $noNota,
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            \App\Models\AuditLog::log('create', "Penjualan {$noNota} - Rp " . number_format($grandTotal, 0, ',', '.'), 'Sale', $sale->id);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'sale' => $sale->load('details.product'),
                'no_nota' => $noNota,
                'grand_total' => $grandTotal,
                'kembalian' => max(0, $kembalian),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    // Daftar penjualan
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'user', 'details']);

        if ($request->filled('search')) {
            $query->where('no_nota', 'like', "%{$request->search}%");
        }

        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('tipe_penjualan')) {
            $tipe = $request->tipe_penjualan;
            $query->whereHas('details', function ($q) use ($tipe) {
                $q->where('tipe_harga', $tipe === 'resep' ? 'resep' : 'hv');
            });
        }

        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('sales.index', compact('sales'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'details.product']);
        return view('sales.show', compact('sale'));
    }

    // Void nota
    public function void(Sale $sale)
    {
        if ($sale->status === 'void') {
            return back()->with('error', 'Nota sudah di-void.');
        }

        DB::beginTransaction();
        try {
            // Kembalikan stok
            foreach ($sale->details as $detail) {
                $product = $detail->product;
                $stokSebelum = $product->stok;
                $product->increment('stok', $detail->jumlah);

                StockCard::create([
                    'product_id' => $product->id,
                    'tipe' => 'masuk',
                    'jumlah' => $detail->jumlah,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSebelum + $detail->jumlah,
                    'referensi' => $sale->no_nota,
                    'keterangan' => 'Void penjualan',
                    'user_id' => auth()->id(),
                ]);
            }

            $sale->update(['status' => 'void']);

            // Catat cash flow void
            CashFlow::create([
                'tanggal' => now()->toDateString(),
                'tipe' => 'kredit',
                'nominal' => $sale->grand_total,
                'keterangan' => "Void penjualan {$sale->no_nota}",
                'referensi' => $sale->no_nota,
                'user_id' => auth()->id(),
            ]);

            DB::commit();
            \App\Models\AuditLog::log('void', "Void nota {$sale->no_nota} - Rp " . number_format($sale->grand_total, 0, ',', '.'), 'Sale', $sale->id);
            return back()->with('success', 'Nota berhasil di-void.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal void nota: ' . $e->getMessage());
        }
    }

    // Cetak struk
    public function printReceipt(Sale $sale)
    {
        $sale->load(['customer', 'user', 'details.product']);
        return view('sales.receipt', compact('sale'));
    }

    // API: Autocomplete data pasien resep berdasarkan nama
    public function searchPasien(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $pasiens = Sale::whereNotNull('pasien_nama')
            ->where('pasien_nama', '!=', '')
            ->where('pasien_nama', 'like', "%{$query}%")
            ->select('pasien_nama', 'pasien_no_hp', 'pasien_alamat')
            ->groupBy('pasien_nama', 'pasien_no_hp', 'pasien_alamat')
            ->orderBy('pasien_nama')
            ->limit(10)
            ->get();

        return response()->json($pasiens);
    }
}
