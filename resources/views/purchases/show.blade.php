@extends('layouts.app')

@section('title', 'Detail Pembelian')
@section('page-title', 'Detail Pembelian: ' . $purchase->no_faktur)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div><p class="text-xs text-gray-500">No. Faktur</p><p class="font-bold">{{ $purchase->no_faktur }}</p></div>
            <div><p class="text-xs text-gray-500">Tanggal</p><p class="font-medium">{{ $purchase->tanggal_faktur->format('d/m/Y') }}</p></div>
            <div><p class="text-xs text-gray-500">Supplier</p><p class="font-medium">{{ $purchase->supplier->nama }}</p></div>
            <div><p class="text-xs text-gray-500">Jatuh Tempo</p><p class="font-medium">{{ $purchase->tanggal_jatuh_tempo?->format('d/m/Y') }}</p></div>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left py-2 px-3">#</th>
                    <th class="text-left py-2 px-3">Barang</th>
                    <th class="text-center py-2 px-3">Qty</th>
                    <th class="text-right py-2 px-3">Harga</th>
                    <th class="text-center py-2 px-3">Diskon</th>
                    <th class="text-right py-2 px-3">Subtotal</th>

                </tr>
            </thead>
            <tbody>
                @foreach($purchase->details as $i => $detail)
                <tr class="border-b border-gray-50">
                    <td class="py-2 px-3">{{ $i + 1 }}</td>
                    <td class="py-2 px-3 font-medium">{{ $detail->product->nama_barang }}</td>
                    <td class="py-2 px-3 text-center">{{ $detail->jumlah }}</td>
                    <td class="py-2 px-3 text-right">Rp {{ number_format($detail->harga_beli, 0, ',', '.') }}</td>
                    <td class="py-2 px-3 text-center">{{ $detail->diskon_persen }}%</td>
                    <td class="py-2 px-3 text-right font-medium">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>

                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 w-64 ml-auto space-y-1 text-sm">
            <div class="flex justify-between"><span>Subtotal</span><span>Rp {{ number_format($purchase->subtotal, 0, ',', '.') }}</span></div>
            <div class="flex justify-between text-red-600"><span>Diskon</span><span>- Rp {{ number_format($purchase->diskon_total, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span>PPN 10%</span><span>Rp {{ number_format($purchase->ppn, 0, ',', '.') }}</span></div>
            <div class="flex justify-between font-bold text-lg border-t pt-2"><span>Total</span><span class="text-indigo-600">Rp {{ number_format($purchase->grand_total, 0, ',', '.') }}</span></div>
        </div>
    </div>

    @if($purchase->returns->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-3"><i class="fas fa-undo text-orange-500 mr-2"></i>Retur Pembelian</h3>
        @foreach($purchase->returns as $return)
        <div class="bg-orange-50 rounded-lg p-3 mb-2">
            <p class="font-medium">{{ $return->no_retur }} - {{ $return->tanggal->format('d/m/Y') }}</p>
            <p class="text-sm text-gray-600">Total: Rp {{ number_format($return->total, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-600">Alasan: {{ $return->alasan ?? '-' }}</p>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
