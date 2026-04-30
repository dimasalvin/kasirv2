@extends('layouts.app')

@section('title', 'Edit Barang')
@section('page-title', 'Edit Barang: ' . $product->nama_barang)

@section('breadcrumb')
<li><span class="mx-1">/</span></li>
<li><a href="{{ route('products.index') }}" class="hover:text-gray-700">Data Barang</a></li>
<li><span class="mx-1">/</span></li>
<li class="text-gray-800 font-medium">Edit: {{ $product->nama_barang }}</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('products.update', $product) }}" x-data="productForm()" class="track-changes">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Barang *</label>
                    <input type="text" name="kode_barang" value="{{ old('kode_barang', $product->kode_barang) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>



                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang *</label>
                    <input type="text" name="nama_barang" value="{{ old('nama_barang', $product->nama_barang) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan *</label>
                    <select name="satuan" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach(['pcs','strip','box','botol','tube','sachet','tablet','kapsul'] as $s)
                            <option value="{{ $s }}" {{ old('satuan', $product->satuan) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pabrik</label>
                    <input type="text" name="pabrik" value="{{ old('pabrik', $product->pabrik) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grup Obat *</label>
                    <select name="grup" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="hijau" {{ old('grup', $product->grup) == 'hijau' ? 'selected' : '' }}>🟢 Hijau - Obat Bebas</option>
                        <option value="merah" {{ old('grup', $product->grup) == 'merah' ? 'selected' : '' }}>🔴 Merah - Obat Keras</option>
                        <option value="biru" {{ old('grup', $product->grup) == 'biru' ? 'selected' : '' }}>🔵 Biru - Konsinyasi</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas Terapi</label>
                    <input type="text" name="kelas_terapi" value="{{ old('kelas_terapi', $product->kelas_terapi) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Rak</label>
                    <input type="text" name="lokasi_rak" value="{{ old('lokasi_rak', $product->lokasi_rak) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="md:col-span-2 border-t border-gray-200 pt-4">
                    <h3 class="text-lg font-semibold text-gray-800"><i class="fas fa-boxes text-indigo-500 mr-2"></i>Stok & Harga</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Minimum *</label>
                    <input type="text" name="stok_minimum" value="{{ old('stok_minimum', $product->stok_minimum) }}" inputmode="numeric" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 money-format">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli (HNA) *</label>
                    <input type="text" name="harga_beli" x-money="hna" @input="hitungHarga()" inputmode="numeric" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="flex items-center">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Aktif</span>
                    </label>
                </div>

                <div class="md:col-span-2 bg-indigo-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-indigo-800 mb-3"><i class="fas fa-calculator mr-1"></i> Kalkulasi Harga Otomatis</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-indigo-600">Harga Jual (HNA + PPN 10%)</p>
                            <p class="text-lg font-bold text-indigo-800" x-text="'Rp ' + formatNumber(hargaJual)">Rp 0</p>
                        </div>
                        <div>
                            <p class="text-xs text-indigo-600">Harga HV (+10%)</p>
                            <p class="text-lg font-bold text-indigo-800" x-text="'Rp ' + formatNumber(hargaHV)">Rp 0</p>
                        </div>
                        <div>
                            <p class="text-xs text-indigo-600">Harga Resep (+8%)</p>
                            <p class="text-lg font-bold text-indigo-800" x-text="'Rp ' + formatNumber(hargaResep)">Rp 0</p>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('keterangan', $product->keterangan) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">Batal</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                    <i class="fas fa-save mr-1"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function productForm() {
    return {
        hna: {{ old('harga_beli', $product->harga_beli) }},
        hargaJual: 0, hargaHV: 0, hargaResep: 0,
        init() { this.hitungHarga(); },
        hitungHarga() {
            this.hargaJual = Math.round(this.hna * 1.10);
            this.hargaHV = Math.round(this.hargaJual * 1.10);
            this.hargaResep = Math.round(this.hargaHV * 1.08);
        },
        formatNumber(num) { return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }
    }
}
</script>
@endpush
@endsection
