@extends('layouts.app')

@section('title', 'Detail Penjualan')
@section('page-title', 'Detail Nota: ' . $sale->no_nota)

@section('breadcrumb')
<li><span class="mx-1">/</span></li>
<li><a href="{{ route('sales.index') }}" class="hover:text-gray-700">Penjualan</a></li>
<li><span class="mx-1">/</span></li>
<li class="text-gray-800 font-medium">{{ $sale->no_nota }}</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <!-- Header -->
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $sale->no_nota }}</h2>
                <p class="text-sm text-gray-500">{{ $sale->tanggal->format('d F Y') }} | {{ $sale->jam }}</p>
            </div>
            <div class="flex space-x-2">
                @if($sale->status == 'completed')
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">Selesai</span>
                @else
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">Void</span>
                @endif
                <a href="{{ route('sales.print', $sale) }}" target="_blank" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">
                    <i class="fas fa-print mr-1"></i> Cetak
                </a>
            </div>
        </div>

        <!-- Info -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Kasir</p>
                <p class="font-medium text-sm">{{ $sale->user->name ?? '-' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Pelanggan</p>
                <p class="font-medium text-sm">{{ $sale->customer->nama ?? 'Umum' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Tipe</p>
                <p class="font-medium text-sm capitalize">{{ $sale->tipe_penjualan }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Shift</p>
                <p class="font-medium text-sm capitalize">{{ $sale->shift }}</p>
            </div>
        </div>

        @if($sale->pasien_nama)
        <div class="mb-4 bg-purple-50 rounded-lg p-3">
            <div class="flex items-center mb-1">
                <i class="fas fa-user-injured text-purple-500 mr-2"></i>
                <p class="text-xs font-semibold text-purple-700">Data Pasien Resep</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-2">
                <div>
                    <p class="text-xs text-purple-500">Nama Pasien</p>
                    <p class="font-medium text-sm text-purple-800">{{ $sale->pasien_nama }}</p>
                </div>
                <div>
                    <p class="text-xs text-purple-500">No. HP</p>
                    <p class="font-medium text-sm text-purple-800">{{ $sale->pasien_no_hp ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-purple-500">Alamat</p>
                    <p class="font-medium text-sm text-purple-800">{{ $sale->pasien_alamat ?? '-' }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Items -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-2 px-3">#</th>
                        <th class="text-left py-2 px-3">Barang</th>
                        <th class="text-center py-2 px-3">Tipe</th>
                        <th class="text-right py-2 px-3">Harga</th>
                        <th class="text-center py-2 px-3">Qty</th>
                        <th class="text-center py-2 px-3">Diskon</th>
                        <th class="text-right py-2 px-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->details as $i => $detail)
                    <tr class="border-b border-gray-50">
                        <td class="py-2 px-3">{{ $i + 1 }}</td>
                        <td class="py-2 px-3">
                            <p class="font-medium">{{ $detail->product->nama_barang }}</p>
                            <p class="text-xs text-gray-500">{{ $detail->product->kode_barang }}</p>
                        </td>
                        <td class="py-2 px-3 text-center">
                            <span class="px-2 py-0.5 rounded text-xs {{ $detail->tipe_harga == 'resep' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ strtoupper($detail->tipe_harga) }}
                            </span>
                        </td>
                        <td class="py-2 px-3 text-right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 text-center">{{ $detail->jumlah }}</td>
                        <td class="py-2 px-3 text-center">{{ $detail->diskon_persen > 0 ? $detail->diskon_persen . '%' : '-' }}</td>
                        <td class="py-2 px-3 text-right font-medium">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="mt-4 border-t pt-4">
            <div class="w-64 ml-auto space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal</span>
                    <span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-red-600">
                    <span>Diskon</span>
                    <span>- Rp {{ number_format($sale->diskon_total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-lg border-t pt-2">
                    <span>Total</span>
                    <span class="text-indigo-600">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Bayar ({{ $sale->metode_bayar == 'tunai' ? 'Tunai' : 'Non Tunai' }})</span>
                    <span>Rp {{ number_format($sale->bayar, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-green-600">
                    <span>Kembalian</span>
                    <span>Rp {{ number_format($sale->kembalian, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
