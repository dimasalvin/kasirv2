# 💊 Apotek POS - Sistem Manajemen Apotek & Kasir

Aplikasi POS Kasir + Manajemen Apotek berbasis **Laravel 10** dengan fitur lengkap seperti sistem apotek profesional.

## 🚀 Fitur Utama

### 1. Auth & Role Management
- Login multi role: **Admin**, **Apoteker**, **Asisten Apoteker**
- **Single Login** — hanya 1 session aktif per user (login dari device lain = force logout device sebelumnya)
- Rate limiting (anti brute force)
- Security headers

### 2. Dashboard
- Total penjualan hari ini
- Stok menipis (alert real-time)
- Transaksi terakhir
- Chart penjualan 7 hari terakhir

### 3. Manajemen Stock
- CRUD data barang (kode, nama, satuan, pabrik, grup, kelas terapi)
- Grup obat: 🟢 Hijau (bebas), 🔴 Merah (keras/narkotika), 🔵 Biru (konsinyasi)
- Kartu stok otomatis
- Stock opname per periode
- Notifikasi stok minimum
- **Riwayat perubahan harga** (otomatis tercatat saat pembelian)

### 4. Setting Harga (Konfigurasi Admin)
- **Persentase bisa diatur manual** di menu Pengaturan:
  - PPN % (default 10%)
  - Markup HV % (default 10%)
  - Markup Resep % (default 8%)
- Preview simulasi real-time

### 5. Supplier (PBF)
- CRUD supplier lengkap
- Jatuh tempo pembayaran
- Riwayat pembelian per supplier

### 6. Pembelian
- Input pembelian berdasarkan faktur
- **Diskon per item: pilih Persentase (%) atau Rupiah (Rp)**
- Hitung otomatis total & netto
- Retur pembelian
- Update stok & harga otomatis + riwayat harga

### 7. Kasir / POS
- Search barang (kode/nama)
- Multi item transaksi
- **Diskon per item: Persen (%) atau Rupiah (Rp)**
- Harga otomatis (HV / Resep)
- Pembayaran tunai & non tunai (EDC/bank)
- Hitung kembalian otomatis + quick amount buttons
- **Keyboard shortcuts**: F1, F2, F5, F8, Esc

### 8. Penjualan Resep
- Data pasien: Nama (wajib), No HP, Alamat
- **Autocomplete** pasien dari riwayat
- Struk **tidak menampilkan daftar obat** (rahasia medis)

### 9. Laporan
- Laporan Penjualan (per tanggal, shift, metode bayar)
- Closing Kasir (2 shift: Pagi & Siang)
- Input Pengeluaran operasional
- Laporan Arus Kas (debit/kredit/saldo)
- Produk Terlaris
- Print-friendly

### 10. Fitur Tambahan
- **Dark Mode** + 6 Warna Aksen
- **Format Ribuan** otomatis di semua input angka
- **Audit Log** — catat semua aktivitas penting
- **Single Login** — 1 device per user
- **Absensi** — clock in/out
- **Buku Manual** — akses di /manual

## 🔒 Keamanan

- Single Login (1 session per user)
- CSRF Protection
- Rate Limiting (login, transaksi, export)
- Security Headers
- Password policy (min 8, mixed case, numbers)
- Role-based access control
- Audit log

## ⚡ Instalasi

```bash
cd kasir_test
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
# Buat database 'apotek_pos' di MySQL
php artisan migrate --seed
php artisan serve
```

## 🔑 Akun Default

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `Admin123!` |
| Apoteker | `apoteker` | `Apoteker123!` |
| Asisten | `asisten` | `Asisten123!` |

## ⌨️ Keyboard Shortcuts (POS)

| Tombol | Fungsi |
|--------|--------|
| `F1` | Focus pencarian |
| `F2` | Focus jumlah bayar |
| `F5` | Transaksi baru |
| `F8` | Proses transaksi |
| `Esc` | Kosongkan |

## 📊 Sistem Shift

| Shift | Jam |
|-------|-----|
| Pagi | 07:00 - 13:59 |
| Siang | 14:00 - 21:00 |

## 💰 Rumus Harga

```
Harga Jual  = HNA × (1 + PPN%)
Harga HV    = Harga Jual × (1 + Markup HV%)
Harga Resep = Harga HV × (1 + Markup Resep%)
```

## 📁 Tech Stack

- **Backend:** Laravel 10 (PHP 8.2)
- **Frontend:** Blade + Tailwind CSS (Vite) + Alpine.js
- **Database:** MySQL
- **Charts:** Chart.js
- **Icons:** Font Awesome 6

## 📝 License

MIT License
