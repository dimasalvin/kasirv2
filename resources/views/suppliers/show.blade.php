@extends('layouts.app')

@section('title', 'Detail Supplier')
@section('page-title', 'Detail Supplier: ' . $supplier->nama)

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div><p class="text-xs text-gray-500">Kode</p><p class="font-medium">{{ $supplier->kode }}</p></div>
            <div><p class="text-xs text-gray-500">Nama</p><p class="font-medium">{{ $supplier->nama }}</p></div>
            <div><p class="text-xs text-gray-500">Kota</p><p class="font-medium">{{ $supplier->kota ?? '-' }}</p></div>
            <div><p class="text-xs text-gray-500">No. Telp</p><p class="font-medium">{{ $supplier->no_telp ?? '-' }}</p></div>
            <div><p class="text-xs text-gray-500">Email</p><p class="font-medium">{{ $supplier->email ?? '-' }}</p></div>
            <div><p class="text-xs text-gray-500">Jatuh Tempo</p><p class="font-medium">{{ $supplier->jatuh_tempo }} hari</p></div>
            <div class="col-span-2"><p class="text-xs text-gray-500">Alamat</p><p class="font-medium">{{ $supplier->alamat ?? '-' }}</p></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold mb-4"><i class="fas fa-history text-indigo-500 mr-2"></i>Riwayat Pembelian</h3>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left py-2 px-3">No. Faktur</th>
                    <th class="text-left py-2 px-3">Tanggal</th>
                    <th class="text-right py-2 px-3">Total</th>
                    <th class="text-center py-2 px-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                <tr class="border-b border-gray-50">
                    <td class="py-2 px-3"><a href="{{ route('purchases.show', $purchase) }}" class="text-indigo-600">{{ $purchase->no_faktur }}</a></td>
                    <td class="py-2 px-3">{{ $purchase->tanggal_faktur->format('d/m/Y') }}</td>
                    <td class="py-2 px-3 text-right">Rp {{ number_format($purchase->grand_total, 0, ',', '.') }}</td>
                    <td class="py-2 px-3 text-center"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">{{ $purchase->status }}</span></td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-4 text-center text-gray-500">Belum ada pembelian</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $purchases->links() }}
    </div>
</div>
@endsection
