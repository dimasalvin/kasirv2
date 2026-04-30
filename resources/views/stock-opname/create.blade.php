@extends('layouts.app')

@section('title', 'Buat Stock Opname')
@section('page-title', 'Buat Stock Opname Baru')

@section('content')
<div x-data="opnameForm()" class="space-y-4">
    <form method="POST" action="{{ route('stock-opname.store') }}" @submit="prepareSubmit($event)">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Opname</label>
                    <input type="text" name="kode_opname" value="{{ $kodeOpname }}" readonly class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <input type="text" name="catatan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
            <div class="flex gap-3 mb-3 items-end">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Pilih Barang <span class="text-red-500">*</span></label>
                    <select x-model="selectedProduct" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" data-stok="{{ $p->stok }}" data-nama="{{ $p->nama_barang }}">{{ $p->nama_barang }} (Stok: {{ $p->stok }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-36">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Stok Fisik <span class="text-red-500">*</span></label>
                    <input type="number" x-model.number="stokFisik" placeholder="0" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="button" @click="addItem()" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm whitespace-nowrap"><i class="fas fa-plus mr-1"></i> Tambah</button>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-2 px-3">Barang</th>
                        <th class="text-center py-2 px-3">Stok Sistem</th>
                        <th class="text-center py-2 px-3">Stok Fisik</th>
                        <th class="text-center py-2 px-3">Selisih</th>
                        <th class="text-center py-2 px-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in items" :key="index">
                        <tr class="border-b border-gray-50">
                            <td class="py-2 px-3" x-text="item.nama"></td>
                            <td class="py-2 px-3 text-center" x-text="item.stok_sistem"></td>
                            <td class="py-2 px-3 text-center" x-text="item.stok_fisik"></td>
                            <td class="py-2 px-3 text-center font-medium" :class="item.selisih < 0 ? 'text-red-600' : (item.selisih > 0 ? 'text-green-600' : '')" x-text="item.selisih"></td>
                            <td class="py-2 px-3 text-center"><button type="button" @click="items.splice(index, 1)" class="text-red-500"><i class="fas fa-times"></i></button></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <template x-for="(item, index) in items" :key="'h-'+index">
            <div>
                <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.product_id">
                <input type="hidden" :name="'items['+index+'][stok_fisik]'" :value="item.stok_fisik">
            </div>
        </template>

        <div class="flex justify-end mt-4">
            <button type="submit" :disabled="items.length === 0" class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium disabled:bg-gray-300">
                <i class="fas fa-save mr-2"></i> Simpan Stock Opname
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function opnameForm() {
    const products = @json($products);
    return {
        items: [],
        selectedProduct: '',
        stokFisik: 0,
        addItem() {
            if (!this.selectedProduct) return;
            const product = products.find(p => p.id == this.selectedProduct);
            if (!product) return;
            this.items.push({
                product_id: product.id,
                nama: product.nama_barang,
                stok_sistem: product.stok,
                stok_fisik: this.stokFisik,
                selisih: this.stokFisik - product.stok,
            });
            this.selectedProduct = '';
            this.stokFisik = 0;
        },
        prepareSubmit(e) {
            if (this.items.length === 0) { e.preventDefault(); alert('Tambahkan minimal 1 item!'); }
        }
    }
}
</script>
@endpush
@endsection
