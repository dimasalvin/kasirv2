@extends('layouts.app')

@section('title', 'Detail Barang')
@section('page-title', 'Detail: ' . $product->nama_barang)

@section('breadcrumb')
<li><span class="mx-1">/</span></li>
<li><a href="{{ route('products.index') }}" class="hover:text-gray-700">Data Barang</a></li>
<li><span class="mx-1">/</span></li>
<li class="text-gray-800 font-medium">{{ $product->nama_barang }}</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Product Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $product->nama_barang }}</h2>
                <p class="text-sm text-gray-500">{{ $product->kode_barang }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('products.edit', $product) }}" class="px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-lg text-sm hover:bg-yellow-200">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Grup</p>
                <p class="font-semibold text-sm">{{ $product->grup_label }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Satuan</p>
                <p class="font-semibold text-sm">{{ ucfirst($product->satuan) }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Pabrik</p>
                <p class="font-semibold text-sm">{{ $product->pabrik ?? '-' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Kategori</p>
                <p class="font-semibold text-sm">{{ $product->category->nama ?? '-' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Stok</p>
                <p class="font-semibold text-sm {{ $product->stok <= $product->stok_minimum ? 'text-red-600' : 'text-green-600' }}">
                    {{ $product->stok }} {{ $product->satuan }}
                </p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Stok Minimum</p>
                <p class="font-semibold text-sm">{{ $product->stok_minimum }}</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-500">Lokasi Rak</p>
                <p class="font-semibold text-sm">{{ $product->lokasi_rak ?? '-' }}</p>
            </div>
        </div>

        <!-- Harga -->
        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 rounded-lg p-3">
                <p class="text-xs text-blue-600">Harga Beli (HNA)</p>
                <p class="font-bold text-blue-800">Rp {{ number_format($product->harga_beli, 0, ',', '.') }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-3">
                <p class="text-xs text-green-600">Harga Jual</p>
                <p class="font-bold text-green-800">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</p>
            </div>
            <div class="bg-indigo-50 rounded-lg p-3">
                <p class="text-xs text-indigo-600">Harga HV</p>
                <p class="font-bold text-indigo-800">Rp {{ number_format($product->harga_hv, 0, ',', '.') }}</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-3">
                <p class="text-xs text-purple-600">Harga Resep</p>
                <p class="font-bold text-purple-800">Rp {{ number_format($product->harga_resep, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Riwayat Harga -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-line text-green-500 mr-2"></i> Riwayat Perubahan Harga
        </h3>
        @if($priceHistories->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-2 px-3 text-gray-600">Tanggal</th>
                        <th class="text-right py-2 px-3 text-gray-600">HNA Lama</th>
                        <th class="text-right py-2 px-3 text-gray-600">HNA Baru</th>
                        <th class="text-center py-2 px-3 text-gray-600">Perubahan</th>
                        <th class="text-left py-2 px-3 text-gray-600">Referensi</th>
                        <th class="text-left py-2 px-3 text-gray-600">User</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($priceHistories as $ph)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-2 px-3">{{ $ph->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-2 px-3 text-right">Rp {{ number_format($ph->harga_beli_lama, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 text-right font-medium">Rp {{ number_format($ph->harga_beli_baru, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 text-center">
                            @php $diff = $ph->harga_beli_baru - $ph->harga_beli_lama; @endphp
                            <span class="px-2 py-0.5 rounded text-xs {{ $diff > 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ $diff > 0 ? '↑' : '↓' }} {{ number_format(abs($diff), 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="py-2 px-3 font-mono text-xs">{{ $ph->referensi ?? '-' }}</td>
                        <td class="py-2 px-3 text-gray-600">{{ $ph->user->name ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-sm text-gray-500 text-center py-4"><i class="fas fa-info-circle mr-1"></i> Belum ada perubahan harga. Riwayat akan tercatat otomatis saat ada pembelian dengan harga berbeda.</p>
        @endif
    </div>

    <!-- Kartu Stok -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-history text-indigo-500 mr-2"></i> Kartu Stok
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-2 px-3 text-gray-600">Tanggal</th>
                        <th class="text-center py-2 px-3 text-gray-600">Tipe</th>
                        <th class="text-center py-2 px-3 text-gray-600">Jumlah</th>
                        <th class="text-center py-2 px-3 text-gray-600">Stok Sebelum</th>
                        <th class="text-center py-2 px-3 text-gray-600">Stok Sesudah</th>
                        <th class="text-left py-2 px-3 text-gray-600">Referensi</th>
                        <th class="text-left py-2 px-3 text-gray-600">Keterangan</th>
                        <th class="text-left py-2 px-3 text-gray-600">User</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockCards as $card)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-2 px-3">{{ $card->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-2 px-3 text-center">
                            @if($card->tipe == 'masuk')
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">Masuk</span>
                            @elseif($card->tipe == 'keluar')
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">Keluar</span>
                            @elseif($card->tipe == 'opname')
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs">Opname</span>
                            @else
                                <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded text-xs">Retur</span>
                            @endif
                        </td>
                        <td class="py-2 px-3 text-center font-medium">{{ $card->jumlah }}</td>
                        <td class="py-2 px-3 text-center">{{ $card->stok_sebelum }}</td>
                        <td class="py-2 px-3 text-center font-medium">{{ $card->stok_sesudah }}</td>
                        <td class="py-2 px-3 font-mono text-xs">{{ $card->referensi ?? '-' }}</td>
                        <td class="py-2 px-3 text-gray-600">{{ $card->keterangan ?? '-' }}</td>
                        <td class="py-2 px-3 text-gray-600">{{ $card->user->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-4 text-center text-gray-500">Belum ada riwayat stok</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $stockCards->links() }}
        </div>
    </div>
</div>
@endsection
