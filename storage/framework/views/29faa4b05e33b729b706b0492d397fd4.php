<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Manual - Apotek POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            @page { margin: 15mm; size: A4; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
            body { font-size: 11px; }
        }
        body { font-family: 'Segoe UI', sans-serif; }
        h1, h2, h3 { page-break-after: avoid; }
        table { page-break-inside: avoid; }
    </style>
</head>
<body class="bg-white text-gray-800 max-w-4xl mx-auto p-8">

    <!-- Print Button -->
    <div class="no-print fixed top-4 right-4 flex gap-2">
        <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 shadow">
            <i class="fas fa-print mr-1"></i> Cetak / Simpan PDF
        </button>
        <a href="<?php echo e(route('dashboard')); ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 shadow">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <!-- Cover -->
    <div class="text-center py-16 border-b-4 border-indigo-600 mb-8">
        <div class="w-20 h-20 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-prescription-bottle-medical text-indigo-600 text-3xl"></i>
        </div>
        <h1 class="text-4xl font-bold text-gray-900 mb-2">APOTEK POS</h1>
        <h2 class="text-xl text-gray-600 mb-4">Buku Manual Penggunaan</h2>
        <p class="text-sm text-gray-500">Sistem Manajemen Apotek & Point of Sale</p>
        <p class="text-sm text-gray-400 mt-4">Versi 1.0 — <?php echo e(now()->format('F Y')); ?></p>
    </div>

    <!-- Daftar Isi -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b-2 border-gray-200 pb-2">Daftar Isi</h2>
        <ol class="space-y-1 text-sm">
            <li class="flex justify-between"><span>1. Login & Akun</span><span class="text-gray-400">3</span></li>
            <li class="flex justify-between"><span>2. Dashboard</span><span class="text-gray-400">4</span></li>
            <li class="flex justify-between"><span>3. Manajemen Barang / Obat</span><span class="text-gray-400">5</span></li>
            <li class="flex justify-between"><span>4. Supplier (PBF)</span><span class="text-gray-400">6</span></li>
            <li class="flex justify-between"><span>5. Pembelian</span><span class="text-gray-400">7</span></li>
            <li class="flex justify-between"><span>6. Kasir / POS</span><span class="text-gray-400">8</span></li>
            <li class="flex justify-between"><span>7. Penjualan Resep</span><span class="text-gray-400">10</span></li>
            <li class="flex justify-between"><span>8. Laporan</span><span class="text-gray-400">11</span></li>
            <li class="flex justify-between"><span>9. Closing Kasir</span><span class="text-gray-400">12</span></li>
            <li class="flex justify-between"><span>10. Pengaturan Harga</span><span class="text-gray-400">13</span></li>
            <li class="flex justify-between"><span>11. Keyboard Shortcuts</span><span class="text-gray-400">14</span></li>
        </ol>
    </div>

    <!-- BAB 1: Login -->
    <div class="page-break"></div>
    <h2 class="text-2xl font-bold text-indigo-700 mb-4">1. Login & Akun</h2>
    <p class="mb-3">Buka browser dan akses <code class="bg-gray-100 px-2 py-0.5 rounded text-sm">http://127.0.0.1:8000</code></p>
    <h3 class="text-lg font-semibold mt-4 mb-2">Akun Default</h3>
    <table class="w-full text-sm border border-gray-200 mb-4">
        <thead class="bg-gray-50"><tr><th class="p-2 text-left border-b">Role</th><th class="p-2 text-left border-b">Username</th><th class="p-2 text-left border-b">Password</th></tr></thead>
        <tbody>
            <tr><td class="p-2 border-b">Admin</td><td class="p-2 border-b font-mono">admin</td><td class="p-2 border-b font-mono">Admin123!</td></tr>
            <tr><td class="p-2 border-b">Apoteker</td><td class="p-2 border-b font-mono">apoteker</td><td class="p-2 border-b font-mono">Apoteker123!</td></tr>
            <tr><td class="p-2 border-b">Asisten</td><td class="p-2 border-b font-mono">asisten</td><td class="p-2 border-b font-mono">Asisten123!</td></tr>
        </tbody>
    </table>
    <h3 class="text-lg font-semibold mt-4 mb-2">Hak Akses</h3>
    <ul class="list-disc list-inside text-sm space-y-1">
        <li><strong>Admin</strong> — Akses penuh termasuk manajemen user & pengaturan</li>
        <li><strong>Apoteker</strong> — Semua fitur kecuali manajemen user & pengaturan</li>
        <li><strong>Asisten Apoteker</strong> — Sama seperti Apoteker</li>
    </ul>

    <!-- BAB 2: Dashboard -->
    <div class="page-break"></div>
    <h2 class="text-2xl font-bold text-indigo-700 mb-4">2. Dashboard</h2>
    <p class="mb-3">Halaman utama setelah login. Menampilkan ringkasan operasional apotek:</p>
    <ul class="list-disc list-inside text-sm space-y-1 mb-4">
        <li><strong>Penjualan Hari Ini</strong> — Total omzet dan jumlah transaksi</li>
        <li><strong>Total Produk Aktif</strong> — Jumlah item obat yang tersedia</li>
        <li><strong>Stok Menipis</strong> — Produk yang stoknya ≤ minimum (perlu restock)</li>
        <li><strong>Mendekati Expired</strong> — Produk yang expired dalam 30 hari</li>
        <li><strong>Grafik Penjualan 7 Hari</strong> — Tren penjualan harian</li>
        <li><strong>Transaksi Terakhir</strong> — 10 transaksi terbaru</li>
    </ul>

    <!-- BAB 3: Manajemen Barang -->
    <div class="page-break"></div>
    <h2 class="text-2xl font-bold text-indigo-700 mb-4">3. Manajemen Barang / Obat</h2>
    <h3 class="text-lg font-semibold mt-4 mb-2">Tambah Barang Baru</h3>
    <ol class="list-decimal list-inside text-sm space-y-1 mb-4">
        <li>Klik menu <strong>Data Barang</strong> di sidebar</li>
        <li>Klik tombol <strong>"Tambah Barang"</strong></li>
        <li>Isi data: Kode, Nama, Kategori, Satuan, Pabrik, Grup Obat</li>
        <li>Input <strong>Harga Beli (HNA)</strong> — harga jual otomatis terhitung</li>
        <li>Klik <strong>"Simpan"</strong></li>
    </ol>
    <h3 class="text-lg font-semibold mt-4 mb-2">Grup Obat</h3>
    <table class="w-full text-sm border border-gray-200 mb-4">
        <thead class="bg-gray-50"><tr><th class="p-2 text-left border-b">Warna</th><th class="p-2 text-left border-b">Keterangan</th></tr></thead>
        <tbody>
            <tr><td class="p-2 border-b">🟢 Hijau</td><td class="p-2 border-b">Obat Bebas — bisa dijual tanpa resep</td></tr>
            <tr><td class="p-2 border-b">🔴 Merah</td><td class="p-2 border-b">Obat Keras / Narkotika — perlu resep dokter</td></tr>
            <tr><td class="p-2 border-b">🔵 Biru</td><td class="p-2 border-b">Konsinyasi — titipan dari supplier</td></tr>
        </tbody>
    </table>
    <h3 class="text-lg font-semibold mt-4 mb-2">Kartu Stok</h3>
    <p class="text-sm">Setiap pergerakan stok (masuk/keluar/opname/retur) otomatis tercatat di Kartu Stok. Lihat di halaman Detail Barang.</p>

    <!-- BAB 4: Supplier -->
    <div class="page-break"></div>
    <h2 class="text-2xl font-bold text-indigo-700 mb-4">4. Supplier (PBF)</h2>
    <p class="mb-3">Kelola data Pedagang Besar Farmasi (PBF) yang memasok obat ke apotek.</p>
    <ol class="list-decimal list-inside text-sm space-y-1">
        <li>Klik menu <strong>Supplier (PBF)</strong></li>
        <li>Klik <strong>"Tambah Supplier"</strong></li>
        <li>Isi: Kode, Nama, Alamat, Kota, No. Telp, Jatuh Tempo (hari)</li>
        <li><strong>Jatuh Tempo</strong> = batas waktu pembayaran faktur (misal 30 hari)</li>
    </ol>

    <!-- BAB 5: Pembelian -->
    <div class="page-break"></div>
    <h2 class="text-2xl font-bold text-indigo-700 mb-4">5. Pembelian</h2>
    <h3 class="text-lg font-semibold mt-4 mb-2">Input Pembelian</h3>
    <ol class="list-decimal list-inside text-sm space-y-1 mb-4">
        <li>Klik menu <strong>Pembelian</strong> → <strong>"Input Pembelian"</strong></li>
        <li>Isi No. Faktur, Tanggal, pilih Supplier</li>
        <li>Tambahkan item: pilih Barang, Qty, Harga HNA</li>
        <li><strong>Diskon</strong> bisa dipilih: <strong>Persentase (%)</strong> atau <strong>Rupiah (Rp)</strong></li>
        <li>Klik <strong>"Tambah Item"</strong> — ulangi untuk item lain</li>
        <li>Klik <strong>"Simpan Pembelian"</strong></li>
    </ol>
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm mb-4">
        <strong>Otomatis:</strong> Stok barang bertambah, harga beli & harga jual diperbarui, kartu stok tercatat.
    </div>
    <h3 class="text-lg font-semibold mt-4 mb-2">Retur Pembelian</h3>
    <p class="text-sm">Jika ada barang rusak/expired, buka detail pembelian → klik <strong>"Retur"</strong> → pilih item & qty yang diretur.</p>

    <!-- BAB 6: Kasir / POS -->
    <div class="page-break"></div>
    <h2 class="text-2xl font-bold text-indigo-700 mb-4">6. Kasir / POS (Point of Sale)</h2>
    <h3 class="text-lg font-semibold mt-4 mb-2">Alur Transaksi</h3>
    <ol class="list-decimal list-inside text-sm space-y-1 mb-4">
        <li><strong>Cari barang</strong> — ketik nama atau kode barang di kolom pencarian</li>
        <li><strong>Klik barang</strong> dari dropdown → otomatis masuk keranjang</li>
        <li>Atur <strong>qty</strong> dan <strong>diskon</strong> jika perlu</li>
        <li>Pilih <strong>tipe penjualan</strong>: Reguler (HV) atau Resep</li>
        <li>Isi <strong>nama pelanggan</strong> (opsional untuk reguler, wajib untuk resep)</li>
        <li>Pilih <strong>metode bayar</strong>: Tunai atau Non Tunai</li>
        <li>Input <strong>jumlah bayar</strong> (atau klik tombol cepat: Pas, 50K, 100K, dll)</li>
        <li>Klik <strong>"Proses Transaksi"</strong> (atau tekan <kbd>F8</kbd>)</li>
        <li>Klik <strong>"Cetak Struk"</strong> atau <strong>"Transaksi Baru"</strong></li>
    </ol>
    <h3 class="text-lg font-semibold mt-4 mb-2">Keyboard Shortcuts</h3>
    <table class="w-full text-sm border border-gray-200 mb-4">
        <thead class="bg-gray-50"><tr><th class="p-2 text-left border-b">Tombol</th><th class="p-2 text-left border-b">Fungsi</th></tr></thead>
        <tbody>
            <tr><td class="p-2 border-b font-mono">F1</td><td class="p-2 border-b">Focus ke kolom pencarian barang</td></tr>
            <tr><td class="p-2 border-b font-mono">F2</td><td class="p-2 border-b">Focus ke input jumlah bayar</td></tr>
            <tr><td class="p-2 border-b font-mono">F5</td><td class="p-2 border-b">Mulai transaksi baru</td></tr>
            <tr><td class="p-2 border-b font-mono">F8</td><td class="p-2 border-b">Proses transaksi</td></tr>
            <tr><td class="p-2 border-b font-mono">Esc</td><td class="p-2 border-b">Kosongkan pencarian / tutup modal</td></tr>
        </tbody>
    </table>

    <!-- BAB 7: Penjualan Resep -->
    <div class="page-break"></div>
    <h2 class="text-2xl font-bold text-indigo-700 mb-4">7. Penjualan Resep</h2>
    <p class="mb-3">Mode khusus untuk melayani resep dokter:</p>
    <ol class="list-decimal list-inside text-sm space-y-1 mb-4">
        <li>Di POS, klik tombol <strong>"Resep"</strong> (ungu)</li>
        <li>Ketik <strong>nama pasien</strong> — jika sudah pernah, data otomatis terisi (autocomplete)</li>
        <li>Isi <strong>No. HP</strong> dan <strong>Alamat</strong> pasien</li>
        <li>Tambahkan obat sesuai resep</li>
        <li>Harga otomatis mengunakan <strong>Harga Resep</strong> (lebih tinggi dari HV)</li>
    </ol>
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 text-sm mb-4">
        <strong>Catatan:</strong> Struk resep <strong>tidak menampilkan daftar obat</strong> (rahasia medis). Hanya menampilkan jumlah item dan total harga.
    </div>

    <!-- BAB 8: Laporan -->
    <div class="page-break"></div>
    <h2 class="text-2xl font-bold text-indigo-700 mb-4">8. Laporan</h2>
    <h3 class="text-lg font-semibold mt-4 mb-2">Laporan Penjualan</h3>
    <p class="text-sm mb-2">Filter berdasarkan: tanggal, shift (pagi/siang/malam), metode bayar (tunai/non-tunai).</p>
    <p class="text-sm mb-4">Menampilkan: total penjualan, breakdown tunai vs non-tunai, HV vs resep, detail per transaksi.</p>

    <h3 class="text-lg font-semibold mt-4 mb-2">Laporan Arus Kas</h3>
    <p class="text-sm mb-2">Menampilkan semua aliran uang masuk (debit) dan keluar (kredit).</p>
    <p class="text-sm mb-4">Saldo = Total Debit - Total Kredit.</p>

    <h3 class="text-lg font-semibold mt-4 mb-2">Produk Terlaris</h3>
    <p class="text-sm mb-4">Ranking produk berdasarkan jumlah terjual dalam periode tertentu.</p>

    <h3 class="text-lg font-semibold mt-4 mb-2">Mencetak Laporan</h3>
    <p class="text-sm">Klik tombol <strong>"Print"</strong> → dialog print browser muncul → pilih printer atau "Save as PDF".</p>

    <!-- BAB 9: Closing Kasir -->
    <div class="page-break"></div>
    <h2 class="text-2xl font-bold text-indigo-700 mb-4">9. Closing Kasir</h2>
    <h3 class="text-lg font-semibold mt-4 mb-2">Alur Kerja</h3>
    <table class="w-full text-sm border border-gray-200 mb-4">
        <thead class="bg-gray-50"><tr><th class="p-2 text-left border-b">Langkah</th><th class="p-2 text-left border-b">Aksi</th></tr></thead>
        <tbody>
            <tr><td class="p-2 border-b font-bold">1. Buka Shift</td><td class="p-2 border-b">Klik "Buka Shift" → pilih shift → input saldo awal (uang di laci)</td></tr>
            <tr><td class="p-2 border-b font-bold">2. Transaksi</td><td class="p-2 border-b">Lakukan penjualan di POS seperti biasa</td></tr>
            <tr><td class="p-2 border-b font-bold">3. Pengeluaran</td><td class="p-2 border-b">Klik "Pengeluaran" → input nominal & keterangan (beli plastik, konsumsi, dll)</td></tr>
            <tr><td class="p-2 border-b font-bold">4. Tutup Shift</td><td class="p-2 border-b">Klik "Tutup" → sistem otomatis rekap</td></tr>
        </tbody>
    </table>
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm">
        <strong>Rumus:</strong> Saldo Akhir = Saldo Awal + Penjualan Tunai − Pengeluaran<br>
        <em>Non-tunai tidak dihitung karena langsung masuk ke rekening bank.</em>
    </div>

    <!-- BAB 10: Pengaturan Harga -->
    <div class="page-break"></div>
    <h2 class="text-2xl font-bold text-indigo-700 mb-4">10. Pengaturan Harga</h2>
    <p class="mb-3">Menu <strong>Pengaturan</strong> (hanya Admin) untuk mengatur persentase markup harga:</p>
    <table class="w-full text-sm border border-gray-200 mb-4">
        <thead class="bg-gray-50"><tr><th class="p-2 text-left border-b">Parameter</th><th class="p-2 text-left border-b">Default</th><th class="p-2 text-left border-b">Keterangan</th></tr></thead>
        <tbody>
            <tr><td class="p-2 border-b">PPN</td><td class="p-2 border-b">10%</td><td class="p-2 border-b">Harga Jual = HNA × (1 + PPN%)</td></tr>
            <tr><td class="p-2 border-b">Markup HV</td><td class="p-2 border-b">10%</td><td class="p-2 border-b">Harga HV = Harga Jual × (1 + Markup%)</td></tr>
            <tr><td class="p-2 border-b">Markup Resep</td><td class="p-2 border-b">8%</td><td class="p-2 border-b">Harga Resep = Harga HV × (1 + Markup%)</td></tr>
        </tbody>
    </table>
    <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-sm">
        <strong>Contoh:</strong> HNA = Rp 10,000<br>
        Harga Jual = 10,000 × 1.10 = Rp 11,000<br>
        Harga HV = 11,000 × 1.10 = Rp 12,100<br>
        Harga Resep = 12,100 × 1.08 = Rp 13,068
    </div>

    <!-- BAB 11: Shortcuts & Tips -->
    <div class="page-break"></div>
    <h2 class="text-2xl font-bold text-indigo-700 mb-4">11. Tips & Fitur Tambahan</h2>
    
    <h3 class="text-lg font-semibold mt-4 mb-2">Dark Mode</h3>
    <p class="text-sm mb-4">Klik ikon 🌙 di navbar untuk beralih ke mode gelap. Klik ikon 🎨 untuk memilih warna aksen.</p>

    <h3 class="text-lg font-semibold mt-4 mb-2">Format Angka</h3>
    <p class="text-sm mb-4">Semua input uang otomatis terformat ribuan (contoh: 1,000,000). Cukup ketik angka, format otomatis.</p>

    <h3 class="text-lg font-semibold mt-4 mb-2">Void Nota</h3>
    <p class="text-sm mb-4">Jika ada kesalahan transaksi, buka Daftar Penjualan → klik ikon 🚫 → konfirmasi void. Stok otomatis dikembalikan.</p>

    <h3 class="text-lg font-semibold mt-4 mb-2">Stock Opname</h3>
    <ol class="list-decimal list-inside text-sm space-y-1 mb-4">
        <li>Klik menu <strong>Stock Opname</strong> → <strong>"Buat Stock Opname"</strong></li>
        <li>Pilih barang → input stok fisik (hasil hitung manual)</li>
        <li>Sistem otomatis hitung selisih</li>
        <li>Klik <strong>"Setujui"</strong> untuk update stok di sistem</li>
    </ol>

    <!-- Footer -->
    <div class="page-break"></div>
    <div class="text-center py-16 border-t-4 border-indigo-600 mt-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Apotek POS v1.0</h2>
        <p class="text-gray-500">Sistem Manajemen Apotek & Point of Sale</p>
        <p class="text-gray-400 text-sm mt-4">Dokumen ini digenerate otomatis.<br>Untuk bantuan lebih lanjut, hubungi administrator sistem.</p>
    </div>

</body>
</html>
<?php /**PATH C:\WORK\other\kasir_test\resources\views/manual.blade.php ENDPATH**/ ?>