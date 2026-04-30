@extends('layouts.app')

@section('title', 'Pembelian')
@section('page-title', 'Data Pembelian')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-gray-500 text-sm">Kelola pembelian barang dari supplier</p>
        <a href="{{ route('purchases.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
            <i class="fas fa-plus mr-2"></i> Input Pembelian
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs text-gray-500 mb-1">No. Faktur</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="w-48">
                <label class="block text-xs text-gray-500 mb-1">Supplier</label>
                <select name="supplier_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm"><i class="fas fa-search mr-1"></i> Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-3 px-4 text-gray-600">No. Faktur</th>
                        <th class="text-left py-3 px-4 text-gray-600">Tanggal</th>
                        <th class="text-left py-3 px-4 text-gray-600">Supplier</th>
                        <th class="text-right py-3 px-4 text-gray-600">Total</th>
                        <th class="text-center py-3 px-4 text-gray-600">Status Bayar</th>
                        <th class="text-left py-3 px-4 text-gray-600">Jatuh Tempo</th>
                        <th class="text-center py-3 px-4 text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-3 px-4 font-mono font-medium">
                            <a href="{{ route('purchases.show', $purchase) }}" class="text-indigo-600 hover:text-indigo-800">{{ $purchase->no_faktur }}</a>
                        </td>
                        <td class="py-3 px-4 text-gray-600">{{ $purchase->tanggal_faktur->format('d/m/Y') }}</td>
                        <td class="py-3 px-4">{{ $purchase->supplier->nama }}</td>
                        <td class="py-3 px-4 text-right font-medium">Rp {{ number_format($purchase->grand_total, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($purchase->status_bayar == 'lunas')
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">Lunas</span>
                            @elseif($purchase->status_bayar == 'sebagian')
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs">Sebagian</span>
                            @else
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">Belum Bayar</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-gray-600">{{ $purchase->tanggal_jatuh_tempo?->format('d/m/Y') ?? '-' }}</td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('purchases.show', $purchase) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('purchases.return', $purchase) }}" class="p-1.5 text-orange-600 hover:bg-orange-50 rounded" title="Retur"><i class="fas fa-undo"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-8 text-center text-gray-500">Belum ada data pembelian</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $purchases->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
