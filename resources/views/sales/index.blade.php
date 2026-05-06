@extends('layouts.app')

@section('title', 'Daftar Penjualan')
@section('page-title', 'Daftar Penjualan')

@section('content')
<div class="space-y-6">
    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="w-40">
                <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="w-40">
                <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="w-36">
                <label class="block text-xs text-gray-500 mb-1">Tipe</label>
                <select name="tipe_penjualan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua</option>
                    <option value="reguler" {{ request('tipe_penjualan') == 'reguler' ? 'selected' : '' }}>HV</option>
                    <option value="resep" {{ request('tipe_penjualan') == 'resep' ? 'selected' : '' }}>Resep</option>
                </select>
            </div>
            <div class="w-36">
                <label class="block text-xs text-gray-500 mb-1">Metode</label>
                <select name="metode_bayar" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua</option>
                    <option value="tunai" {{ request('metode_bayar') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="non_tunai" {{ request('metode_bayar') == 'non_tunai' ? 'selected' : '' }}>Non Tunai</option>
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs text-gray-500 mb-1">Cari No. Nota</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="INV-..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-3 px-4 text-gray-600">No. Nota</th>
                        <th class="text-left py-3 px-4 text-gray-600">Tanggal</th>
                        <th class="text-left py-3 px-4 text-gray-600">Kasir</th>
                        <th class="text-center py-3 px-4 text-gray-600">Tipe</th>
                        <th class="text-center py-3 px-4 text-gray-600">Metode</th>
                        <th class="text-right py-3 px-4 text-gray-600">Total</th>
                        <th class="text-center py-3 px-4 text-gray-600">Status</th>
                        <th class="text-center py-3 px-4 text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-800 font-mono font-medium">
                                {{ $sale->no_nota }}
                            </a>
                        </td>
                        <td class="py-3 px-4 text-gray-600">{{ $sale->tanggal->format('d/m/Y') }} {{ $sale->jam }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $sale->user->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($sale->details->where('tipe_harga', 'resep')->count() > 0 && $sale->details->where('tipe_harga', 'hv')->count() > 0)
                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded text-xs">HV+Resep</span>
                            @elseif($sale->details->where('tipe_harga', 'resep')->count() > 0)
                                <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-xs">Resep</span>
                            @else
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">HV</span>
                            @endif
                            @if($sale->has_stok_minus)
                                <span class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-xs ml-1" title="Transaksi dengan stok minus">⚠</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($sale->metode_bayar == 'tunai')
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">Tunai</span>
                            @else
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">Non Tunai</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right font-medium">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($sale->status == 'completed')
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">Selesai</span>
                            @else
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">Void</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center space-x-1">
                                <a href="{{ route('sales.show', $sale) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('sales.print', $sale) }}" target="_blank" class="p-1.5 text-gray-600 hover:bg-gray-50 rounded"><i class="fas fa-print"></i></a>
                                @if($sale->status == 'completed')
                                <form method="POST" action="{{ route('sales.void', $sale) }}" class="inline" onsubmit="return confirm('Void nota ini?')">
                                    @csrf
                                    <button class="p-1.5 text-red-600 hover:bg-red-50 rounded"><i class="fas fa-ban"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500">Belum ada data penjualan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $sales->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
