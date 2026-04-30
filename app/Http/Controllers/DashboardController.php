<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\CashFlow;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Total penjualan hari ini
        $totalPenjualanHariIni = Sale::where('tanggal', $today)
            ->where('status', 'completed')
            ->sum('grand_total');

        // Jumlah transaksi hari ini
        $jumlahTransaksiHariIni = Sale::where('tanggal', $today)
            ->where('status', 'completed')
            ->count();

        // Stok menipis
        $jumlahStokMenipis = Product::stokMenipis()->where('is_active', true)->count();
        $stokMenipis = Product::stokMenipis()
            ->where('is_active', true)
            ->orderBy('stok', 'asc')
            ->limit(10)
            ->get();

        // Transaksi terakhir
        $transaksiTerakhir = Sale::with(['customer', 'user'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Penjualan 7 hari terakhir (untuk chart)
        $penjualan7Hari = Sale::where('status', 'completed')
            ->where('tanggal', '>=', $today->copy()->subDays(6))
            ->selectRaw('tanggal, SUM(grand_total) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Total produk
        $totalProduk = Product::where('is_active', true)->count();

        return view('dashboard', compact(
            'totalPenjualanHariIni',
            'jumlahTransaksiHariIni',
            'jumlahStokMenipis',
            'stokMenipis',
            'transaksiTerakhir',
            'penjualan7Hari',
            'totalProduk'
        ));
    }
}
