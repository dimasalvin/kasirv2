@extends('layouts.app')

@section('title', 'Kasir POS')
@section('page-title', 'Kasir / Point of Sale')

@section('content')
<div x-data="posApp()" @keydown.window="handleShortcut($event)" class="space-y-4">
    <!-- Shortcut hints -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg px-3 py-1.5 flex items-center gap-4 text-xs text-gray-500 no-print">
        <span><kbd class="px-1.5 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xs font-mono">F1</kbd> Cari</span>
        <span><kbd class="px-1.5 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xs font-mono">F2</kbd> Bayar</span>
        <span><kbd class="px-1.5 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xs font-mono">F5</kbd> Transaksi Baru</span>
        <span><kbd class="px-1.5 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xs font-mono">F8</kbd> Proses</span>
        <span><kbd class="px-1.5 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xs font-mono">Esc</kbd> Kosongkan</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Left: Product Search & Cart -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Search Bar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <label class="block text-xs font-medium text-gray-500 mb-1"><i class="fas fa-search mr-1"></i> Cari Barang <span class="text-gray-400">(F1)</span></label>
                <div class="flex gap-3">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" x-model="searchQuery" @keyup.enter="searchProduct()" @input.debounce.250ms="filterProducts()"
                               placeholder="Ketik kode atau nama barang..."
                               class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                               autofocus x-ref="searchInput">
                    </div>
                    <button @click="searchProduct()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <!-- Quick Product List -->
                <div x-show="filteredProducts.length > 0 && searchQuery.length > 1" x-cloak
                     class="mt-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg">
                    <template x-for="product in filteredProducts.slice(0, 10)" :key="product.id">
                        <div @click="addToCart(product)" class="px-3 py-2 item-hover cursor-pointer border-b border-gray-50 flex justify-between items-center">
                            <div>
                                <span class="text-sm font-medium" x-text="product.nama_barang"></span>
                                <span class="text-xs text-gray-500 ml-2" x-text="product.kode_barang"></span>
                                <span x-show="product.stok <= 0" class="ml-2 px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-xs font-medium">HABIS</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500">Stok: </span>
                                <span class="text-xs font-medium" :class="product.stok <= 0 ? 'text-red-600' : ''" x-text="product.stok"></span>
                                <span class="text-sm font-medium text-indigo-600 ml-2" x-text="'Rp ' + formatNumber(product.harga_hv)"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Resep Cart (temporary, only visible in resep mode) -->
            <div x-show="modeResep && resepCart.length > 0" x-cloak class="bg-purple-50 rounded-xl shadow-sm border border-purple-200 overflow-hidden">
                <div class="p-4 border-b border-purple-200 flex items-center justify-between">
                    <h3 class="font-semibold text-purple-800">
                        <i class="fas fa-prescription text-purple-500 mr-2"></i>
                        Racikan Resep (<span x-text="resepCart.length"></span> obat)
                    </h3>
                    <button @click="clearResepCart()" class="text-sm text-red-600 hover:text-red-800">
                        <i class="fas fa-trash mr-1"></i> Kosongkan
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-purple-100">
                            <tr>
                                <th class="text-left py-2 px-3 text-purple-700">Obat</th>
                                <th class="text-right py-2 px-3 text-purple-700 w-28">Harga Resep</th>
                                <th class="text-center py-2 px-3 text-purple-700 w-24">Qty</th>
                                <th class="text-right py-2 px-3 text-purple-700 w-32">Subtotal</th>
                                <th class="text-center py-2 px-3 text-purple-700 w-12"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in resepCart" :key="index">
                                <tr class="border-b border-purple-100 hover:bg-purple-50">
                                    <td class="py-2 px-3">
                                        <p class="font-medium text-gray-800" x-text="item.nama_barang"></p>
                                        <p class="text-xs text-gray-500" x-text="item.kode_barang"></p>
                                    </td>
                                    <td class="py-2 px-3 text-right text-gray-800" x-text="'Rp ' + formatNumber(item.harga_satuan)"></td>
                                    <td class="py-2 px-3 text-center">
                                        <div class="flex items-center justify-center space-x-1">
                                            <button @click="decreaseResepQty(index)" class="w-6 h-6 bg-purple-200 rounded text-xs hover:bg-purple-300">-</button>
                                            <input type="number" x-model.number="item.jumlah" @change="calculateResepSubtotal(index)" min="1"
                                                   class="w-12 text-center border border-purple-300 rounded text-xs py-1">
                                            <button @click="increaseResepQty(index)" class="w-6 h-6 bg-purple-200 rounded text-xs hover:bg-purple-300">+</button>
                                        </div>
                                    </td>
                                    <td class="py-2 px-3 text-right font-medium text-gray-800" x-text="'Rp ' + formatNumber(item.subtotal)"></td>
                                    <td class="py-2 px-3 text-center">
                                        <button @click="removeFromResepCart(index)" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-purple-200 flex items-center justify-between">
                    <div class="text-sm text-purple-700">
                        Total Resep: <span class="font-bold text-lg" x-text="'Rp ' + formatNumber(resepTotal)"></span>
                    </div>
                    <button @click="confirmResep()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium text-sm">
                        <i class="fas fa-check mr-1"></i> Konfirmasi Resep
                    </button>
                </div>
            </div>

            <!-- Cart Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">
                        <i class="fas fa-shopping-cart text-indigo-500 mr-2"></i>
                        Keranjang (<span x-text="cart.length"></span> item)
                    </h3>
                    <button @click="clearCart()" x-show="cart.length > 0" class="text-sm text-red-600 hover:text-red-800">
                        <i class="fas fa-trash mr-1"></i> Kosongkan
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left py-2 px-3 text-gray-600">Barang</th>
                                <th class="text-center py-2 px-3 text-gray-600 w-24">Tipe Harga</th>
                                <th class="text-right py-2 px-3 text-gray-600 w-28">Harga</th>
                                <th class="text-center py-2 px-3 text-gray-600 w-24">Qty</th>
                                <th class="text-center py-2 px-3 text-gray-600 w-28">Diskon</th>
                                <th class="text-right py-2 px-3 text-gray-600 w-32">Subtotal</th>
                                <th class="text-center py-2 px-3 text-gray-600 w-12"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in cart" :key="index">
                                <tr class="border-b border-gray-50 hover:bg-gray-50">
                                    <td class="py-2 px-3">
                                        <p class="font-medium text-gray-800" x-text="item.nama_barang"></p>
                                        <p class="text-xs text-gray-500" x-text="item.kode_barang"></p>
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <span class="text-xs px-2 py-1 rounded bg-indigo-100 text-indigo-700" x-show="!item.is_resep">HV</span>
                                        <span class="text-xs px-2 py-1 rounded bg-purple-100 text-purple-700" x-show="item.is_resep">Resep</span>
                                    </td>
                                    <td class="py-2 px-3 text-right text-gray-800" x-text="'Rp ' + formatNumber(item.harga_satuan)"></td>
                                    <td class="py-2 px-3 text-center">
                                        <template x-if="!item.is_resep">
                                            <div class="flex items-center justify-center space-x-1">
                                                <button @click="decreaseQty(index)" class="w-6 h-6 bg-gray-200 rounded text-xs hover:bg-gray-300" :aria-label="'Kurangi qty ' + item.nama_barang">-</button>
                                                <input type="number" x-model.number="item.jumlah" @change="calculateSubtotal(index)" min="1"
                                                       :aria-label="'Jumlah ' + item.nama_barang"
                                                       class="w-12 text-center border border-gray-300 rounded text-xs py-1">
                                                <button @click="increaseQty(index)" class="w-6 h-6 bg-gray-200 rounded text-xs hover:bg-gray-300" :aria-label="'Tambah qty ' + item.nama_barang">+</button>
                                            </div>
                                        </template>
                                        <template x-if="item.is_resep">
                                            <span class="text-xs text-purple-600 font-medium">1</span>
                                        </template>
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <div class="flex items-center justify-center gap-0.5">
                                            <input type="number" x-model.number="item.diskon_value" @change="calculateSubtotal(index)" min="0"
                                                   :max="item.diskon_tipe === 'persen' ? 100 : item.harga_satuan * item.jumlah"
                                                   :aria-label="'Diskon ' + item.nama_barang"
                                                   class="w-12 text-center border border-gray-300 rounded-l text-xs py-1">
                                            <select x-model="item.diskon_tipe" @change="calculateSubtotal(index)"
                                                    class="border border-gray-300 rounded-r text-xs py-1 px-1 bg-gray-50">
                                                <option value="persen">%</option>
                                                <option value="rupiah">Rp</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="py-2 px-3 text-right font-medium text-gray-800" x-text="'Rp ' + formatNumber(item.subtotal)"></td>
                                    <td class="py-2 px-3 text-center">
                                        <button @click="removeFromCart(index)" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div x-show="cart.length === 0" class="py-12 text-center text-gray-400">
                    <i class="fas fa-shopping-cart text-4xl mb-2"></i>
                    <p>Keranjang kosong</p>
                    <p class="text-xs">Cari barang untuk memulai transaksi</p>
                </div>
            </div>
        </div>

        <!-- Right: Payment Panel -->
        <div class="space-y-4">
            <!-- Mode Input -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Mode Input</label>
                <div class="grid grid-cols-2 gap-2">
                    <button @click="modeResep = false" 
                            :class="!modeResep ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-store mr-1"></i> Reguler (HV)
                    </button>
                    <button @click="modeResep = true" 
                            :class="modeResep ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-prescription mr-1"></i> Resep
                    </button>
                </div>

                <!-- Info Resep Mode -->
                <div x-show="modeResep" x-cloak class="mt-3">
                    <div class="bg-purple-50 rounded-lg p-2 mb-2">
                        <p class="text-xs text-purple-700"><i class="fas fa-info-circle mr-1"></i> Pilih obat resep, lalu klik "Konfirmasi Resep" untuk memasukkan ke keranjang sebagai 1 item Resep.</p>
                    </div>
                    <!-- Data Pasien Resep (autocomplete) -->
                    <div class="space-y-2">
                        <div class="relative">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Nama Pasien <span class="text-red-500">*</span></label>
                            <input type="text" x-model="pasienNama" @input.debounce.300ms="searchPasien()" @focus="showPasienDropdown = true"
                                   placeholder="Ketik nama pasien..."
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <!-- Autocomplete dropdown -->
                            <div x-show="showPasienDropdown && pasienResults.length > 0" @click.away="showPasienDropdown = false" x-cloak
                                 class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                <template x-for="(p, idx) in pasienResults" :key="idx">
                                    <div @click="selectPasien(p)" class="px-3 py-2 item-hover cursor-pointer border-b border-gray-50">
                                        <p class="text-sm font-medium text-gray-800" x-text="p.pasien_nama"></p>
                                        <p class="text-xs text-gray-500">
                                            <span x-show="p.pasien_no_hp"><i class="fas fa-phone mr-1"></i><span x-text="p.pasien_no_hp"></span></span>
                                            <span x-show="p.pasien_alamat" class="ml-2"><i class="fas fa-map-marker-alt mr-1"></i><span x-text="p.pasien_alamat"></span></span>
                                        </p>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">No. HP Pasien</label>
                            <input type="text" x-model="pasienNoHp" placeholder="08xxxxxxxxxx"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Alamat Pasien</label>
                            <input type="text" x-model="pasienAlamat" placeholder="Jl. ..."
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>
                </div>

                <!-- Nama Pelanggan (always visible when not resep mode) -->
                <div x-show="!modeResep" x-cloak class="mt-3">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nama Pelanggan <span class="text-gray-400">(opsional)</span></label>
                    <input type="text" x-model="namaPelanggan" placeholder="Kosongkan jika umum..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <h3 class="font-semibold text-gray-800 mb-3">Ringkasan</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium" x-text="'Rp ' + formatNumber(subtotal)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Diskon</span>
                        <span class="font-medium text-red-600" x-text="'- Rp ' + formatNumber(totalDiskon)"></span>
                    </div>
                    <div class="border-t border-gray-200 pt-2 flex justify-between">
                        <span class="font-semibold text-gray-800">TOTAL</span>
                        <span class="text-xl font-bold text-indigo-600" x-text="'Rp ' + formatNumber(grandTotal)"></span>
                    </div>
                </div>
            </div>

            <!-- Payment -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <h3 class="font-semibold text-gray-800 mb-3">Pembayaran</h3>
                
                <div class="space-y-3">
                    <!-- Metode Bayar -->
                    <div class="grid grid-cols-2 gap-2">
                        <button @click="metodeBayar = 'tunai'" 
                                :class="metodeBayar === 'tunai' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700'"
                                class="py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-money-bill mr-1"></i> Tunai
                        </button>
                        <button @click="metodeBayar = 'non_tunai'" 
                                :class="metodeBayar === 'non_tunai' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                                class="py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-credit-card mr-1"></i> Non Tunai
                        </button>
                    </div>

                    <!-- Referensi Non Tunai -->
                    <div x-show="metodeBayar === 'non_tunai'" x-cloak>
                        <label class="block text-xs text-gray-500 mb-1">No. Ref EDC/Bank</label>
                        <input type="text" x-model="referensiBayar" placeholder="No. referensi..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>

                    <!-- Bayar -->
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Jumlah Bayar</label>
                        <input type="text" x-money="bayar" @input="hitungKembalian()" inputmode="numeric"
                               class="w-full border border-gray-300 rounded-lg px-3 py-3 text-lg font-bold text-center focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Quick Amount Buttons -->
                    <div x-show="metodeBayar === 'tunai'" class="grid grid-cols-3 gap-1">
                        <button @click="bayar = grandTotal; hitungKembalian()" class="py-1.5 bg-gray-100 rounded text-xs hover:bg-gray-200">Pas</button>
                        <button @click="bayar = Math.ceil(grandTotal/1000)*1000; hitungKembalian()" class="py-1.5 bg-gray-100 rounded text-xs hover:bg-gray-200">Bulat 1K</button>
                        <button @click="bayar = Math.ceil(grandTotal/10000)*10000; hitungKembalian()" class="py-1.5 bg-gray-100 rounded text-xs hover:bg-gray-200">Bulat 10K</button>
                        <button @click="bayar = 50000; hitungKembalian()" class="py-1.5 bg-gray-100 rounded text-xs hover:bg-gray-200">50K</button>
                        <button @click="bayar = 100000; hitungKembalian()" class="py-1.5 bg-gray-100 rounded text-xs hover:bg-gray-200">100K</button>
                        <button @click="bayar = 200000; hitungKembalian()" class="py-1.5 bg-gray-100 rounded text-xs hover:bg-gray-200">200K</button>
                    </div>

                    <!-- Kembalian -->
                    <div class="bg-green-50 rounded-lg p-3" x-show="Math.round(kembalian) >= 0 && bayar > 0">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-green-700">Kembalian</span>
                            <span class="text-xl font-bold text-green-700" x-text="'Rp ' + formatNumber(Math.max(0, Math.round(kembalian)))"></span>
                        </div>
                    </div>
                    <div class="bg-red-50 rounded-lg p-3" x-show="Math.round(kembalian) < 0 && bayar > 0">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-red-700">Kurang</span>
                            <span class="text-xl font-bold text-red-700" x-text="'Rp ' + formatNumber(Math.abs(Math.round(kembalian)))"></span>
                        </div>
                    </div>

                    <!-- Process Button -->
                    <button @click="processTransaction()" 
                            :disabled="cart.length === 0 || (metodeBayar === 'tunai' && Math.round(bayar) < Math.round(grandTotal)) || processing"
                            class="w-full py-3 bg-green-600 text-white rounded-lg font-semibold text-lg hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors">
                        <span x-show="!processing"><i class="fas fa-check-circle mr-2"></i> Proses Transaksi</span>
                        <span x-show="processing"><i class="fas fa-spinner fa-spin mr-2"></i> Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Print Iframe -->
    <iframe id="print-frame" style="display:none; position:absolute; width:0; height:0; border:0;"></iframe>

    <!-- Success Modal -->
    <div x-show="showSuccessModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Transaksi Berhasil!</h3>
            <p class="text-gray-600 mb-1">No. Nota: <span class="font-mono font-bold" x-text="lastNoNota"></span></p>
            <p class="text-gray-600 mb-1">Total: <span class="font-bold text-indigo-600" x-text="'Rp ' + formatNumber(lastGrandTotal)"></span></p>
            <p class="text-gray-600 mb-4">Kembalian: <span class="font-bold text-green-600" x-text="'Rp ' + formatNumber(lastKembalian)"></span></p>
            <div class="flex space-x-3">
                <button @click="printReceipt()" class="flex-1 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-print mr-1"></i> Cetak Struk
                </button>
                <button @click="newTransaction()" class="flex-1 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-plus mr-1"></i> Transaksi Baru
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function posApp() {
    return {
        products: @json($products),
        cart: [],
        resepCart: [],
        modeResep: false,
        searchQuery: '',
        filteredProducts: [],
        customerId: '',
        namaDokter: '',
        namaPelanggan: '',
        // Data pasien resep
        pasienNama: '',
        pasienNoHp: '',
        pasienAlamat: '',
        pasienResults: [],
        showPasienDropdown: false,
        // Payment
        metodeBayar: 'tunai',
        referensiBayar: '',
        bayar: 0,
        kembalian: 0,
        processing: false,
        showSuccessModal: false,
        lastNoNota: '',
        lastGrandTotal: 0,
        lastKembalian: 0,
        lastSaleId: null,

        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.harga_satuan * item.jumlah), 0);
        },
        get totalDiskon() {
            return this.cart.reduce((sum, item) => {
                const gross = item.harga_satuan * item.jumlah;
                if (item.diskon_tipe === 'rupiah') {
                    return sum + Math.min(item.diskon_value || 0, gross);
                }
                return sum + (gross * ((item.diskon_value || 0) / 100));
            }, 0);
        },
        get grandTotal() {
            return this.subtotal - this.totalDiskon;
        },

        filterProducts() {
            if (this.searchQuery.length < 2) {
                this.filteredProducts = [];
                return;
            }
            const q = this.searchQuery.toLowerCase();
            this.filteredProducts = this.products.filter(p => 
                p.nama_barang.toLowerCase().includes(q) ||
                p.kode_barang.toLowerCase().includes(q)
            );
        },

        searchProduct() {
            const q = this.searchQuery.toLowerCase().trim();
            const product = this.products.find(p => 
                p.kode_barang.toLowerCase() === q
            );
            if (product) {
                this.addToCart(product);
                this.searchQuery = '';
                this.filteredProducts = [];
            }
        },

        addToCart(product) {
            // If in resep mode, add to resep cart instead
            if (this.modeResep) {
                this.addToResepCart(product);
                return;
            }

            const existing = this.cart.find(item => item.product_id === product.id && !item.is_resep);
            if (existing) {
                if (existing.jumlah >= product.stok) {
                    if (!confirm(`Stok ${product.nama_barang} akan MINUS (tersedia: ${product.stok}). Lanjutkan?`)) {
                        return;
                    }
                }
                existing.jumlah++;
                this.calculateSubtotal(this.cart.indexOf(existing));
                return;
            }

            // Warn if stock is 0 or less
            if (product.stok <= 0) {
                if (!confirm(`Stok ${product.nama_barang} HABIS (stok: ${product.stok}). Lanjutkan dengan stok minus?`)) {
                    return;
                }
            }

            const harga = parseFloat(product.harga_hv);

            this.cart.push({
                product_id: product.id,
                kode_barang: product.kode_barang,
                nama_barang: product.nama_barang,
                stok: product.stok,
                harga_hv: parseFloat(product.harga_hv),
                harga_resep: parseFloat(product.harga_resep),
                harga_satuan: harga,
                tipe_harga: 'hv',
                jumlah: 1,
                diskon_tipe: 'persen',
                diskon_value: 0,
                diskon_persen: 0,
                subtotal: harga,
                is_resep: false,
            });

            this.searchQuery = '';
            this.filteredProducts = [];
            this.$refs.searchInput.focus();
        },

        // Resep cart methods
        addToResepCart(product) {
            const existing = this.resepCart.find(item => item.product_id === product.id);
            if (existing) {
                if (existing.jumlah >= product.stok) {
                    if (!confirm(`Stok ${product.nama_barang} akan MINUS (tersedia: ${product.stok}). Lanjutkan?`)) {
                        return;
                    }
                }
                existing.jumlah++;
                this.calculateResepSubtotal(this.resepCart.indexOf(existing));
                return;
            }

            // Warn if stock is 0 or less
            if (product.stok <= 0) {
                if (!confirm(`Stok ${product.nama_barang} HABIS (stok: ${product.stok}). Lanjutkan dengan stok minus?`)) {
                    return;
                }
            }

            this.resepCart.push({
                product_id: product.id,
                kode_barang: product.kode_barang,
                nama_barang: product.nama_barang,
                stok: product.stok,
                harga_satuan: parseFloat(product.harga_resep),
                jumlah: 1,
                subtotal: parseFloat(product.harga_resep),
            });

            this.searchQuery = '';
            this.filteredProducts = [];
            this.$refs.searchInput.focus();
        },

        get resepTotal() {
            return this.resepCart.reduce((sum, item) => sum + item.subtotal, 0);
        },

        calculateResepSubtotal(index) {
            const item = this.resepCart[index];
            item.subtotal = item.harga_satuan * item.jumlah;
        },

        increaseResepQty(index) {
            const item = this.resepCart[index];
            if (item.jumlah < item.stok) {
                item.jumlah++;
                this.calculateResepSubtotal(index);
            }
        },

        decreaseResepQty(index) {
            const item = this.resepCart[index];
            if (item.jumlah > 1) {
                item.jumlah--;
                this.calculateResepSubtotal(index);
            }
        },

        removeFromResepCart(index) {
            this.resepCart.splice(index, 1);
        },

        clearResepCart() {
            if (confirm('Kosongkan racikan resep?')) {
                this.resepCart = [];
            }
        },

        confirmResep() {
            if (this.resepCart.length === 0) {
                alert('Belum ada obat di racikan resep!');
                return;
            }
            if (!this.pasienNama.trim()) {
                alert('Nama pasien wajib diisi untuk resep!');
                return;
            }

            const totalResep = this.resepTotal;
            const resepItems = this.resepCart.map(item => ({
                product_id: item.product_id,
                jumlah: item.jumlah,
                harga_satuan: item.harga_satuan,
            }));

            // Add single "Resep" item to main cart
            this.cart.push({
                product_id: null,
                kode_barang: 'RESEP',
                nama_barang: 'Resep - ' + this.pasienNama,
                stok: 999,
                harga_hv: totalResep,
                harga_resep: totalResep,
                harga_satuan: totalResep,
                tipe_harga: 'resep',
                jumlah: 1,
                diskon_tipe: 'persen',
                diskon_value: 0,
                diskon_persen: 0,
                subtotal: totalResep,
                is_resep: true,
                resep_items: resepItems,
                resep_pasien_nama: this.pasienNama,
                resep_pasien_no_hp: this.pasienNoHp,
                resep_pasien_alamat: this.pasienAlamat,
            });

            // Clear resep cart and switch back to reguler mode
            this.resepCart = [];
            this.modeResep = false;
            this.pasienNama = '';
            this.pasienNoHp = '';
            this.pasienAlamat = '';
        },

        updateItemPrice(index) {
            const item = this.cart[index];
            item.harga_satuan = item.tipe_harga === 'resep' ? item.harga_resep : item.harga_hv;
            this.calculateSubtotal(index);
        },

        calculateSubtotal(index) {
            const item = this.cart[index];
            const gross = item.harga_satuan * item.jumlah;
            let diskon = 0;
            if (item.diskon_tipe === 'rupiah') {
                diskon = Math.min(item.diskon_value || 0, gross);
                item.diskon_persen = gross > 0 ? (diskon / gross) * 100 : 0;
            } else {
                const persen = Math.min(Math.max(item.diskon_value || 0, 0), 100);
                diskon = gross * (persen / 100);
                item.diskon_persen = persen;
            }
            item.subtotal = gross - diskon;
        },

        increaseQty(index) {
            const item = this.cart[index];
            if (item.jumlah < item.stok) {
                item.jumlah++;
                this.calculateSubtotal(index);
            }
        },

        decreaseQty(index) {
            const item = this.cart[index];
            if (item.jumlah > 1) {
                item.jumlah--;
                this.calculateSubtotal(index);
            }
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        clearCart() {
            if (confirm('Kosongkan keranjang?')) {
                this.cart = [];
            }
        },



        // Autocomplete pasien resep
        async searchPasien() {
            if (this.pasienNama.length < 2) {
                this.pasienResults = [];
                return;
            }
            try {
                const response = await fetch(`/api/pasien/search?q=${encodeURIComponent(this.pasienNama)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                this.pasienResults = await response.json();
                this.showPasienDropdown = true;
            } catch (e) {
                this.pasienResults = [];
            }
        },

        selectPasien(pasien) {
            this.pasienNama = pasien.pasien_nama || '';
            this.pasienNoHp = pasien.pasien_no_hp || '';
            this.pasienAlamat = pasien.pasien_alamat || '';
            this.showPasienDropdown = false;
            this.pasienResults = [];
        },

        hitungKembalian() {
            this.kembalian = Math.round(this.bayar - this.grandTotal);
        },

        async processTransaction() {
            if (this.cart.length === 0) return;
            if (this.metodeBayar === 'tunai' && Math.round(this.bayar) < Math.round(this.grandTotal)) {
                alert('Pembayaran kurang!');
                return;
            }

            this.processing = true;

            // Build items: regular items + expanded resep items
            const items = [];
            const resepGroups = [];

            this.cart.forEach(item => {
                if (item.is_resep && item.resep_items) {
                    // Resep: send individual products for stock deduction
                    resepGroups.push({
                        items: item.resep_items,
                        pasien_nama: item.resep_pasien_nama,
                        pasien_no_hp: item.resep_pasien_no_hp,
                        pasien_alamat: item.resep_pasien_alamat,
                        total: item.harga_satuan,
                        diskon_persen: item.diskon_persen || 0,
                    });
                } else {
                    items.push({
                        product_id: item.product_id,
                        jumlah: item.jumlah,
                        tipe_harga: 'hv',
                        diskon_persen: item.diskon_persen,
                    });
                }
            });

            const data = {
                items: items,
                resep_groups: resepGroups,
                bayar: this.metodeBayar === 'non_tunai' ? this.grandTotal : this.bayar,
                metode_bayar: this.metodeBayar,
                tipe_penjualan: 'reguler',
                customer_id: this.customerId || null,
                referensi_bayar: this.referensiBayar,
                nama_dokter: this.namaDokter,
                nama_pelanggan: this.namaPelanggan || null,
            };

            try {
                const response = await fetch('{{ route("pos.transaction") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data),
                });

                const result = await response.json();

                if (result.success) {
                    this.lastNoNota = result.no_nota;
                    this.lastGrandTotal = result.grand_total;
                    this.lastKembalian = result.kembalian;
                    this.lastSaleId = result.sale.id;
                    this.showSuccessModal = true;
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
            } finally {
                this.processing = false;
            }
        },

        printReceipt() {
            if (this.lastSaleId) {
                const frame = document.getElementById('print-frame');
                frame.src = '/sales/' + this.lastSaleId + '/print';
                frame.onload = function() {
                    frame.contentWindow.focus();
                    frame.contentWindow.print();
                };
            }
        },

        newTransaction() {
            this.cart = [];
            this.resepCart = [];
            this.modeResep = false;
            this.bayar = 0;
            this.kembalian = 0;
            this.customerId = '';
            this.namaDokter = '';
            this.namaPelanggan = '';
            this.pasienNama = '';
            this.pasienNoHp = '';
            this.pasienAlamat = '';
            this.pasienResults = [];
            this.referensiBayar = '';
            this.showSuccessModal = false;
            this.$refs.searchInput.focus();
            location.reload();
        },

        // Keyboard Shortcuts
        handleShortcut(e) {
            // Jangan handle jika sedang di input (kecuali F-keys)
            const isInput = ['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName);

            switch(e.key) {
                case 'F1':
                    e.preventDefault();
                    this.$refs.searchInput.focus();
                    this.$refs.searchInput.select();
                    break;
                case 'F2':
                    e.preventDefault();
                    // Focus ke input bayar
                    document.querySelector('[x-money="bayar"]')?.focus();
                    break;
                case 'F5':
                    e.preventDefault();
                    if (this.showSuccessModal || confirm('Mulai transaksi baru?')) {
                        this.newTransaction();
                    }
                    break;
                case 'F8':
                    e.preventDefault();
                    this.processTransaction();
                    break;
                case 'Escape':
                    e.preventDefault();
                    if (this.showSuccessModal) {
                        this.newTransaction();
                    } else if (this.searchQuery) {
                        this.searchQuery = '';
                        this.filteredProducts = [];
                    } else if (this.cart.length > 0) {
                        if (confirm('Kosongkan keranjang?')) {
                            this.cart = [];
                            this.bayar = 0;
                            this.kembalian = 0;
                        }
                    }
                    break;
            }
        },

        formatNumber(num) {
            return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    }
}
</script>
@endpush
@endsection
