@extends('layouts.app')

@section('title', 'Tambah Barang')
@section('page-title', 'Tambah Barang Baru')

@section('breadcrumb')
<li><span class="mx-1">/</span></li>
<li><a href="{{ route('products.index') }}" class="hover:text-gray-700">Data Barang</a></li>
<li><span class="mx-1">/</span></li>
<li class="text-gray-800 font-medium">Tambah Baru</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('products.store') }}" x-data="productForm()" class="track-changes">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kode Barang -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Barang *</label>
                    <input type="text" name="kode_barang" value="{{ old('kode_barang') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>



                <!-- Nama Barang -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang *</label>
                    <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Satuan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan *</label>
                    <select name="satuan" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="pcs" {{ old('satuan') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                        <option value="strip" {{ old('satuan') == 'strip' ? 'selected' : '' }}>Strip</option>
                        <option value="box" {{ old('satuan') == 'box' ? 'selected' : '' }}>Box</option>
                        <option value="botol" {{ old('satuan') == 'botol' ? 'selected' : '' }}>Botol</option>
                        <option value="tube" {{ old('satuan') == 'tube' ? 'selected' : '' }}>Tube</option>
                        <option value="sachet" {{ old('satuan') == 'sachet' ? 'selected' : '' }}>Sachet</option>
                        <option value="tablet" {{ old('satuan') == 'tablet' ? 'selected' : '' }}>Tablet</option>
                        <option value="kapsul" {{ old('satuan') == 'kapsul' ? 'selected' : '' }}>Kapsul</option>
                    </select>
                </div>

                <!-- Pabrik -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pabrik</label>
                    <input type="text" name="pabrik" value="{{ old('pabrik') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Grup -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grup Obat *</label>
                    <select name="grup" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="hijau" {{ old('grup') == 'hijau' ? 'selected' : '' }}>🟢 Hijau - Obat Bebas</option>
                        <option value="merah" {{ old('grup') == 'merah' ? 'selected' : '' }}>🔴 Merah - Obat Keras / Narkotika</option>
                        <option value="biru" {{ old('grup') == 'biru' ? 'selected' : '' }}>🔵 Biru - Konsinyasi</option>
                    </select>
                </div>

                <!-- Kelas Terapi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas Terapi</label>
                    <input type="text" name="kelas_terapi" value="{{ old('kelas_terapi') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Contoh: Analgesik, Antibiotik">
                </div>

                <!-- Lokasi Rak -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Rak</label>
                    <input type="text" name="lokasi_rak" value="{{ old('lokasi_rak') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Contoh: Rak A-01">
                </div>

                <!-- Divider Stok -->
                <div class="md:col-span-2 border-t border-gray-200 pt-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-boxes text-indigo-500 mr-2"></i>Stok & Harga
                    </h3>
                </div>

                <!-- Stok -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Awal *</label>
                    <input type="text" name="stok" value="{{ old('stok', 0) }}" inputmode="numeric" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 money-format">
                </div>

                <!-- Stok Minimum -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Minimum *</label>
                    <input type="text" name="stok_minimum" value="{{ old('stok_minimum', 5) }}" inputmode="numeric" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 money-format">
                    <p class="text-xs text-gray-500 mt-1">Alert jika stok <= nilai ini</p>
                </div>

                <!-- Harga Beli (HNA) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli (HNA) *</label>
                    <input type="text" name="harga_beli" x-money="hna" @input="hitungHarga()" inputmode="numeric" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>



                <!-- Harga Otomatis -->
                <div class="md:col-span-2 bg-indigo-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-indigo-800 mb-3">
                        <i class="fas fa-calculator mr-1"></i> Kalkulasi Harga Otomatis
                    </h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-indigo-600">Harga Jual (HNA + PPN 10%)</p>
                            <p class="text-lg font-bold text-indigo-800" x-text="'Rp ' + formatNumber(hargaJual)">Rp 0</p>
                        </div>
                        <div>
                            <p class="text-xs text-indigo-600">Harga HV (+10% dari Jual)</p>
                            <p class="text-lg font-bold text-indigo-800" x-text="'Rp ' + formatNumber(hargaHV)">Rp 0</p>
                        </div>
                        <div>
                            <p class="text-xs text-indigo-600">Harga Resep (+8% dari HV)</p>
                            <p class="text-lg font-bold text-indigo-800" x-text="'Rp ' + formatNumber(hargaResep)">Rp 0</p>
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function productForm() {
    return {
        hna: {{ old('harga_beli', 0) }},
        hargaJual: 0,
        hargaHV: 0,
        hargaResep: 0,
        init() {
            this.hitungHarga();
        },
        hitungHarga() {
            this.hargaJual = Math.round(this.hna * 1.10);
            this.hargaHV = Math.round(this.hargaJual * 1.10);
            this.hargaResep = Math.round(this.hargaHV * 1.08);
        },
        formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    }
}
</script>
@endpush
@endsection
