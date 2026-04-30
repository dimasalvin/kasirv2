@extends('layouts.app')

@section('title', 'Input Pembelian')
@section('page-title', 'Input Pembelian Baru')

@section('breadcrumb')
<li><span class="mx-1">/</span></li>
<li><a href="{{ route('purchases.index') }}" class="hover:text-gray-700">Pembelian</a></li>
<li><span class="mx-1">/</span></li>
<li class="text-gray-800 font-medium">Input Baru</li>
@endsection

@section('content')
<div x-data="purchaseForm()" class="space-y-4">
    <form method="POST" action="{{ route('purchases.store') }}" @submit="prepareSubmit($event)">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Left: Form Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-file-invoice text-indigo-500 mr-2"></i>Info Faktur</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Faktur *</label>
                        <input type="text" name="no_faktur" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Faktur *</label>
                        <input type="date" name="tanggal_faktur" value="{{ date('Y-m-d') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier *</label>
                        <select name="supplier_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->kode }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="catatan" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
            </div>

            <!-- Right: Items -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Add Item -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <h3 class="font-semibold text-gray-800 mb-3"><i class="fas fa-plus-circle text-green-500 mr-2"></i>Tambah Item</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div class="lg:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Barang <span class="text-red-500">*</span></label>
                            <select x-model="newItem.product_id" @change="onProductChange()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Pilih Barang --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" data-harga="{{ $p->harga_beli }}">{{ $p->nama_barang }} ({{ $p->kode_barang }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Jumlah (Qty) <span class="text-red-500">*</span></label>
                            <input type="number" x-model.number="newItem.jumlah" min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Harga Beli (HNA) <span class="text-red-500">*</span></label>
                            <input type="text" x-money="newItem.harga_beli" inputmode="numeric" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Diskon</label>
                            <div class="flex gap-1">
                                <input type="number" x-model.number="newItem.diskon_value" min="0" step="0.1"
                                       :max="newItem.diskon_tipe === 'persen' ? 100 : 999999999"
                                       :placeholder="newItem.diskon_tipe === 'persen' ? '0' : '0'"
                                       class="flex-1 border border-gray-300 rounded-l-lg px-2 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                                <select x-model="newItem.diskon_tipe" class="border border-gray-300 rounded-r-lg px-2 py-2 text-xs bg-gray-50 focus:ring-2 focus:ring-indigo-500">
                                    <option value="persen">%</option>
                                    <option value="rupiah">Rp</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">No. Batch</label>
                            <input type="text" x-model="newItem.batch_number" placeholder="Contoh: B20260430" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="lg:col-span-2 flex items-end">
                            <!-- Preview subtotal -->
                            <div class="flex-1 mr-2">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Preview Subtotal</label>
                                <div class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm font-semibold text-indigo-700"
                                     x-text="'Rp ' + formatNumber(previewSubtotal)">
                                </div>
                            </div>
                            <button type="button" @click="addItem()" class="px-5 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 font-medium whitespace-nowrap">
                                <i class="fas fa-plus mr-1"></i> Tambah Item
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="text-left py-2 px-3">Barang</th>
                                <th class="text-center py-2 px-3">Qty</th>
                                <th class="text-right py-2 px-3">Harga</th>
                                <th class="text-center py-2 px-3">Diskon</th>
                                <th class="text-right py-2 px-3">Subtotal</th>
                                <th class="text-center py-2 px-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="border-b border-gray-50">
                                    <td class="py-2 px-3" x-text="item.nama"></td>
                                    <td class="py-2 px-3 text-center" x-text="item.jumlah"></td>
                                    <td class="py-2 px-3 text-right" x-text="'Rp ' + formatNumber(item.harga_beli)"></td>
                                    <td class="py-2 px-3 text-center text-xs" x-text="item.diskon_label || '-'"></td>
                                    <td class="py-2 px-3 text-right font-medium" x-text="'Rp ' + formatNumber(item.subtotal)"></td>
                                    <td class="py-2 px-3 text-center">
                                        <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="py-2 px-3 text-right font-semibold">Grand Total:</td>
                                <td class="py-2 px-3 text-right font-bold text-indigo-600" x-text="'Rp ' + formatNumber(grandTotal)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Hidden inputs for items -->
                <template x-for="(item, index) in items" :key="'input-'+index">
                    <div>
                        <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.product_id">
                        <input type="hidden" :name="'items['+index+'][jumlah]'" :value="item.jumlah">
                        <input type="hidden" :name="'items['+index+'][harga_beli]'" :value="item.harga_beli">
                        <input type="hidden" :name="'items['+index+'][diskon_persen]'" :value="item.diskon_persen">
                        <input type="hidden" :name="'items['+index+'][expired_date]'" :value="item.expired_date">
                        <input type="hidden" :name="'items['+index+'][batch_number]'" :value="item.batch_number">
                    </div>
                </template>

                <div class="flex justify-end">
                    <button type="submit" :disabled="items.length === 0" class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 disabled:bg-gray-300">
                        <i class="fas fa-save mr-2"></i> Simpan Pembelian
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function purchaseForm() {
    const products = @json($products);
    return {
        items: [],
        newItem: { product_id: '', jumlah: 1, harga_beli: 0, diskon_tipe: 'persen', diskon_value: 0, expired_date: '', batch_number: '' },

        get grandTotal() {
            return this.items.reduce((sum, item) => sum + item.subtotal, 0);
        },

        get previewSubtotal() {
            const gross = (this.newItem.harga_beli || 0) * (this.newItem.jumlah || 0);
            const diskon = this.calcDiskon(gross, this.newItem.diskon_tipe, this.newItem.diskon_value);
            return Math.round(gross - diskon);
        },

        calcDiskon(gross, tipe, value) {
            if (!value || value <= 0) return 0;
            if (tipe === 'persen') {
                return gross * (Math.min(value, 100) / 100);
            } else {
                return Math.min(value, gross); // Rupiah, max = gross
            }
        },

        calcDiskonPersen(gross, tipe, value) {
            if (!value || value <= 0) return 0;
            if (tipe === 'persen') return Math.min(value, 100);
            return gross > 0 ? (Math.min(value, gross) / gross) * 100 : 0;
        },

        onProductChange() {
            const product = products.find(p => p.id == this.newItem.product_id);
            if (product) {
                this.newItem.harga_beli = parseFloat(product.harga_beli);
            }
        },

        addItem() {
            if (!this.newItem.product_id) {
                alert('Pilih barang terlebih dahulu!');
                return;
            }
            if (!this.newItem.jumlah || this.newItem.jumlah < 1) {
                alert('Jumlah harus minimal 1!');
                return;
            }
            if (!this.newItem.harga_beli || this.newItem.harga_beli <= 0) {
                alert('Harga beli harus lebih dari 0!');
                return;
            }

            const product = products.find(p => p.id == this.newItem.product_id);
            const gross = this.newItem.harga_beli * this.newItem.jumlah;
            const diskonNominal = this.calcDiskon(gross, this.newItem.diskon_tipe, this.newItem.diskon_value);
            const diskonPersen = this.calcDiskonPersen(gross, this.newItem.diskon_tipe, this.newItem.diskon_value);

            this.items.push({
                product_id: this.newItem.product_id,
                nama: product ? product.nama_barang : '',
                jumlah: this.newItem.jumlah,
                harga_beli: this.newItem.harga_beli,
                diskon_persen: Math.round(diskonPersen * 100) / 100,
                diskon_label: this.newItem.diskon_tipe === 'persen' 
                    ? (this.newItem.diskon_value || 0) + '%' 
                    : 'Rp ' + this.formatNumber(this.newItem.diskon_value || 0),
                subtotal: Math.round(gross - diskonNominal),
                expired_date: this.newItem.expired_date,
                batch_number: this.newItem.batch_number,
            });

            // Reset form
            this.newItem = { product_id: '', jumlah: 1, harga_beli: 0, diskon_tipe: 'persen', diskon_value: 0, expired_date: '', batch_number: '' };
        },

        removeItem(index) { this.items.splice(index, 1); },

        prepareSubmit(e) {
            if (this.items.length === 0) {
                e.preventDefault();
                alert('Tambahkan minimal 1 item!');
            }
        },

        formatNumber(num) { return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }
    }
}
</script>
@endpush
@endsection
