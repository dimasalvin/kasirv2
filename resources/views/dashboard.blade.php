@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Penjualan Hari Ini -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Penjualan Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($totalPenjualanHariIni, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center text-sm text-gray-500">
                <i class="fas fa-receipt mr-1"></i>
                {{ $jumlahTransaksiHariIni }} transaksi
            </div>
        </div>

        <!-- Total Produk -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Produk Aktif</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalProduk) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-pills text-blue-600 text-xl"></i>
                </div>
            </div>
            <a href="{{ route('products.index') }}" class="mt-3 flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                <i class="fas fa-arrow-right mr-1"></i> Lihat semua
            </a>
        </div>

        <!-- Stok Menipis -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Stok Menipis</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $jumlahStokMenipis }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
            <a href="{{ route('products.index', ['stok_menipis' => 1]) }}" class="mt-3 flex items-center text-sm text-red-600 hover:text-red-800">
                <i class="fas fa-arrow-right mr-1"></i> Perlu restock
            </a>
        </div>

        <!-- Jumlah Transaksi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Transaksi Hari Ini</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $jumlahTransaksiHariIni }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-receipt text-yellow-600 text-xl"></i>
                </div>
            </div>
            <a href="{{ route('sales.index') }}" class="mt-3 flex items-center text-sm text-yellow-600 hover:text-yellow-800">
                <i class="fas fa-arrow-right mr-1"></i> Lihat detail
            </a>
        </div>
    </div>

    <!-- Charts & Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart Penjualan 7 Hari -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chart-line text-indigo-500 mr-2"></i>
                Penjualan 7 Hari Terakhir
            </h3>
            <canvas id="salesChart" height="200"></canvas>
        </div>

        <!-- Stok Menipis -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                Stok Menipis
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 text-gray-600">Barang</th>
                            <th class="text-center py-2 text-gray-600">Stok</th>
                            <th class="text-center py-2 text-gray-600">Min</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stokMenipis as $item)
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="py-2">
                                <p class="font-medium text-gray-800">{{ $item->nama_barang }}</p>
                                <p class="text-xs text-gray-500">{{ $item->kode_barang }}</p>
                            </td>
                            <td class="text-center">
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                    {{ $item->stok }}
                                </span>
                            </td>
                            <td class="text-center text-gray-600">{{ $item->stok_minimum }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500">
                                <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                Semua stok aman
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Transaksi Terakhir -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-history text-indigo-500 mr-2"></i>
                Transaksi Terakhir
            </h3>
            <a href="{{ route('sales.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 text-gray-600">No. Nota</th>
                        <th class="text-left py-3 text-gray-600">Tanggal</th>
                        <th class="text-left py-3 text-gray-600">Kasir</th>
                        <th class="text-left py-3 text-gray-600">Pelanggan</th>
                        <th class="text-right py-3 text-gray-600">Total</th>
                        <th class="text-center py-3 text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksiTerakhir as $trx)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-3">
                            <a href="{{ route('sales.show', $trx) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                {{ $trx->no_nota }}
                            </a>
                        </td>
                        <td class="py-3 text-gray-600">{{ $trx->tanggal->format('d/m/Y') }} {{ $trx->jam }}</td>
                        <td class="py-3 text-gray-600">{{ $trx->user->name ?? '-' }}</td>
                        <td class="py-3 text-gray-600">{{ $trx->customer->nama ?? 'Umum' }}</td>
                        <td class="py-3 text-right font-medium">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                        <td class="py-3 text-center">
                            @if($trx->status === 'completed')
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Selesai</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Void</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500">
                            Belum ada transaksi hari ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Chart Penjualan 7 Hari
    const salesData = @json($penjualan7Hari);
    const labels = salesData.map(item => {
        const date = new Date(item.tanggal);
        return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
    });
    const values = salesData.map(item => parseFloat(item.total));

    // Detect dark mode for chart colors
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.05)';
    const textColor = isDark ? '#9ca3af' : '#6b7280';

    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Penjualan (Rp)',
                data: values,
                borderColor: '#6366f1',
                backgroundColor: isDark ? 'rgba(99, 102, 241, 0.2)' : 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: isDark ? '#1f2937' : '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: {
                        color: textColor,
                        callback: function(value) {
                            return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                        }
                    }
                },
                x: {
                    grid: { color: gridColor },
                    ticks: { color: textColor }
                }
            }
        }
    });
</script>
@endpush
@endsection
