

<?php $__env->startSection('title', 'Kasir POS'); ?>
<?php $__env->startSection('page-title', 'Kasir / Point of Sale'); ?>

<?php $__env->startSection('content'); ?>
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
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500">Stok: </span>
                                <span class="text-xs font-medium" x-text="product.stok"></span>
                                <span class="text-sm font-medium text-indigo-600 ml-2" x-text="'Rp ' + formatNumber(product.harga_hv)"></span>
                            </div>
                        </div>
                    </template>
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
                                        <select x-model="item.tipe_harga" @change="updateItemPrice(index)"
                                                :aria-label="'Tipe harga ' + item.nama_barang"
                                                class="border border-gray-300 rounded px-2 py-1 text-xs">
                                            <option value="hv">HV</option>
                                            <option value="resep">Resep</option>
                                        </select>
                                    </td>
                                    <td class="py-2 px-3 text-right text-gray-800" x-text="'Rp ' + formatNumber(item.harga_satuan)"></td>
                                    <td class="py-2 px-3 text-center">
                                        <div class="flex items-center justify-center space-x-1">
                                            <button @click="decreaseQty(index)" class="w-6 h-6 bg-gray-200 rounded text-xs hover:bg-gray-300" :aria-label="'Kurangi qty ' + item.nama_barang">-</button>
                                            <input type="number" x-model.number="item.jumlah" @change="calculateSubtotal(index)" min="1" :max="item.stok"
                                                   :aria-label="'Jumlah ' + item.nama_barang"
                                                   class="w-12 text-center border border-gray-300 rounded text-xs py-1">
                                            <button @click="increaseQty(index)" class="w-6 h-6 bg-gray-200 rounded text-xs hover:bg-gray-300" :aria-label="'Tambah qty ' + item.nama_barang">+</button>
                                        </div>
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
            <!-- Tipe Penjualan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Penjualan</label>
                <div class="grid grid-cols-2 gap-2">
                    <button @click="tipePenjualan = 'reguler'" 
                            :class="tipePenjualan === 'reguler' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-store mr-1"></i> Reguler (HV)
                    </button>
                    <button @click="tipePenjualan = 'resep'; setAllToResep()" 
                            :class="tipePenjualan === 'resep' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-prescription mr-1"></i> Resep
                    </button>
                </div>

                <!-- Data Pasien Resep (autocomplete) -->
                <div x-show="tipePenjualan === 'resep'" x-cloak class="mt-3 space-y-2">
                    <div class="bg-purple-50 rounded-lg p-2 mb-2">
                        <p class="text-xs text-purple-700"><i class="fas fa-info-circle mr-1"></i> Ketik nama pasien, data akan otomatis terisi jika sudah pernah terdaftar.</p>
                    </div>
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

                <!-- Nama Pelanggan Reguler (opsional, text field) -->
                <div x-show="tipePenjualan === 'reguler'" x-cloak class="mt-3">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nama Pelanggan <span class="text-gray-400">(opsional)</span></label>
                    <input type="text" x-model="pasienNama" placeholder="Kosongkan jika umum..."
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

<?php $__env->startPush('scripts'); ?>
<script>
function posApp() {
    return {
        products: <?php echo json_encode($products, 15, 512) ?>,
        cart: [],
        searchQuery: '',
        filteredProducts: [],
        tipePenjualan: 'reguler',
        customerId: '',
        namaDokter: '',
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
            const existing = this.cart.find(item => item.product_id === product.id);
            if (existing) {
                if (existing.jumlah < product.stok) {
                    existing.jumlah++;
                    this.calculateSubtotal(this.cart.indexOf(existing));
                } else {
                    alert('Stok tidak mencukupi!');
                }
                return;
            }

            const tipeHarga = this.tipePenjualan === 'resep' ? 'resep' : 'hv';
            const harga = tipeHarga === 'resep' ? parseFloat(product.harga_resep) : parseFloat(product.harga_hv);

            this.cart.push({
                product_id: product.id,
                kode_barang: product.kode_barang,
                nama_barang: product.nama_barang,
                stok: product.stok,
                harga_hv: parseFloat(product.harga_hv),
                harga_resep: parseFloat(product.harga_resep),
                harga_satuan: harga,
                tipe_harga: tipeHarga,
                jumlah: 1,
                diskon_tipe: 'persen',
                diskon_value: 0,
                diskon_persen: 0,
                subtotal: harga,
            });

            this.searchQuery = '';
            this.filteredProducts = [];
            this.$refs.searchInput.focus();
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

        setAllToResep() {
            this.cart.forEach((item, index) => {
                item.tipe_harga = 'resep';
                this.updateItemPrice(index);
            });
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
            // Validasi pasien resep
            if (this.tipePenjualan === 'resep' && !this.pasienNama.trim()) {
                alert('Nama pasien wajib diisi untuk penjualan resep!');
                return;
            }

            this.processing = true;

            const data = {
                items: this.cart.map(item => ({
                    product_id: item.product_id,
                    jumlah: item.jumlah,
                    tipe_harga: item.tipe_harga,
                    diskon_persen: item.diskon_persen,
                })),
                bayar: this.metodeBayar === 'non_tunai' ? this.grandTotal : this.bayar,
                metode_bayar: this.metodeBayar,
                tipe_penjualan: this.tipePenjualan,
                customer_id: this.customerId || null,
                referensi_bayar: this.referensiBayar,
                nama_dokter: this.namaDokter,
                pasien_nama: this.pasienNama || null,
                pasien_no_hp: this.pasienNoHp || null,
                pasien_alamat: this.pasienAlamat || null,
            };

            try {
                const response = await fetch('<?php echo e(route("pos.transaction")); ?>', {
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
                window.open('/sales/' + this.lastSaleId + '/print', '_blank');
            }
        },

        newTransaction() {
            this.cart = [];
            this.bayar = 0;
            this.kembalian = 0;
            this.customerId = '';
            this.namaDokter = '';
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\WORK\other\kasir_test\resources\views/pos/index.blade.php ENDPATH**/ ?>