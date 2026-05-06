# 💊 Apotek POS — Sistem Manajemen Apotek & Kasir

Aplikasi **Point of Sale** + Manajemen Apotek berbasis **Laravel 10** (PHP 8.2) dengan fitur lengkap setara sistem apotek profesional.

![Laravel](https://img.shields.io/badge/Laravel-10-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-blue?logo=php)
![Tailwind](https://img.shields.io/badge/Tailwind_CSS-3-06B6D4?logo=tailwindcss)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

---

## 📸 Highlights

- 🏪 POS Kasir lengkap (barcode, multi item, diskon, kembalian otomatis)
- 💊 Manajemen obat dengan grup (Hijau/Merah/Biru) & kelas terapi
- 📊 Dashboard real-time + chart penjualan 7 hari
- 🌙 Dark Mode + 6 warna aksen
- 🔒 Single Login + Audit Log + Rate Limiting
- 📄 Export PDF (Laporan Penjualan & Arus Kas)
- 📖 Buku Manual terintegrasi (13 bab)

---

## 🚀 Fitur Lengkap

### 1. Auth & Role Management
| Fitur | Keterangan |
|-------|-----------|
| Multi Role | Admin, Apoteker, Asisten Apoteker |
| Single Login | 1 session per user (force logout device lain) |
| Rate Limiting | 5 attempt/menit (login), 30/menit (transaksi) |
| Security Headers | X-Content-Type-Options, X-Frame-Options, X-XSS-Protection |
| Password Policy | Min 8 karakter, mixed case, angka |

### 2. Dashboard
- Total penjualan hari ini (tunai + non-tunai)
- Alert stok menipis (di bawah stok minimum)
- Alert produk expired
- 5 transaksi terakhir
- Chart penjualan 7 hari (Chart.js, auto dark mode)

### 3. Manajemen Produk & Stok
- CRUD produk (kode, barcode, nama, satuan, pabrik, kategori, kelas terapi)
- Grup obat: 🟢 Hijau (bebas) · 🔴 Merah (keras/narkotika) · 🔵 Biru (konsinyasi)
- Kartu stok otomatis (masuk/keluar/opname/retur)
- Stock opname per periode (draft → approve)
- Notifikasi stok minimum
- **Riwayat perubahan harga** (tercatat otomatis saat pembelian)
- Lokasi rak & tanggal expired

### 4. Setting Harga (Admin)
- Persentase **bisa diatur manual** di menu Pengaturan:
  - PPN % (default 10%)
  - Markup HV % (default 10%)
  - Markup Resep % (default 8%)
- Preview simulasi harga real-time
- API endpoint untuk frontend: `/api/settings/pricing`

### 5. Supplier (PBF)
- CRUD supplier lengkap (kode, nama, alamat, kota, kontak)
- Jatuh tempo pembayaran (hari)
- Riwayat pembelian per supplier

### 6. Pembelian
- Input pembelian berdasarkan no. faktur
- Multi item per faktur
- **Diskon per item**: pilih Persentase (%) atau Rupiah (Rp)
- Hitung otomatis subtotal, PPN, grand total
- Retur pembelian (partial/full)
- Auto update stok + harga + riwayat harga

### 7. Kasir / POS
- Search barang via kode/barcode/nama
- Multi item transaksi
- **Diskon per item**: Persen (%) atau Rupiah (Rp)
- Harga otomatis (HV untuk reguler, Resep untuk resep)
- Pembayaran: Tunai & Non-Tunai (EDC/transfer bank)
- Hitung kembalian otomatis + quick amount buttons
- Konfirmasi unsaved changes sebelum navigasi
- **Keyboard shortcuts**: F1, F2, F5, F8, Esc

### 8. Penjualan Resep vs Reguler
| Aspek | Reguler | Resep |
|-------|---------|-------|
| Harga | HV | Resep (+markup dari HV) |
| Data pelanggan | Nama (opsional) | Nama (wajib) + No HP + Alamat |
| Autocomplete | ❌ | ✅ (dari riwayat pasien) |
| Struk | Tampilkan daftar obat | **Tanpa** daftar obat (rahasia medis) |

### 9. Laporan & Closing
- **Laporan Penjualan** — filter tanggal, shift, metode bayar + export PDF
- **Closing Kasir** — 2 shift (Pagi 07:00-13:59, Siang 14:00-21:00)
- **Input Pengeluaran** operasional per shift
- **Laporan Arus Kas** — debit/kredit/saldo berjalan + export PDF
- **Produk Terlaris** — ranking per periode
- Semua laporan print-friendly

### 10. Fitur Tambahan
| Fitur | Keterangan |
|-------|-----------|
| 🌙 Dark Mode | Toggle + persist localStorage |
| 🎨 6 Warna Aksen | Indigo, Blue, Emerald, Rose, Amber, Violet |
| 💰 Format Ribuan | Otomatis di semua input angka (Alpine.js `x-money`) |
| 📋 Audit Log | Login, logout, force logout, CRUD, void, pembelian |
| 👤 Single Login | 1 device per user |
| ⏰ Absensi | Clock in/out manual |
| 👥 Manajemen User | CRUD user (admin only) |
| 📖 Buku Manual | 13 bab, akses di `/manual` |
| 🧾 Cetak Struk | Thermal 80mm, auto print |
| 📄 Export PDF | DomPDF (penjualan & arus kas) |

---

## 🔒 Keamanan

- ✅ Single Login (1 session per user, force logout)
- ✅ CSRF Protection (semua form)
- ✅ Rate Limiting (login: 5/min, transaksi: 30/min, export: 10/min)
- ✅ Security Headers (X-Content-Type-Options, X-Frame-Options, X-XSS-Protection)
- ✅ Password Policy (min 8, mixed case, numbers)
- ✅ Role-based Access Control (middleware per route)
- ✅ Audit Log (semua aktivitas penting tercatat)
- ✅ Session Regeneration setelah login
- ✅ Input Validation di semua controller

---

## 📁 Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 10, PHP 8.2 |
| Frontend | Blade Templates, Alpine.js |
| CSS | Tailwind CSS 3 (Vite build + CDN fallback) |
| Database | MySQL 8 (`apotek_pos`) |
| Charts | Chart.js |
| PDF | DomPDF (barryvdh/laravel-dompdf) |
| Icons | Font Awesome 6 |
| Build | Vite |

---

## ⚡ Instalasi

### Prasyarat
- PHP 8.2+ (ext: gd, zip, pdo_mysql, mbstring, openssl)
- Composer 2.x
- Node.js 18+ & NPM
- MySQL 8.x
- XAMPP / Laragon / Docker (opsional)

### Langkah Instalasi

```bash
# 1. Clone repository
git clone <repo-url> kasir_test
cd kasir_test

# 2. Install dependencies
composer install
npm install

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
# DB_DATABASE=apotek_pos
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Buat database & jalankan migration + seeder
php artisan migrate --seed

# 6. Build frontend assets
npm run build

# 7. Jalankan server
php artisan serve
```

Buka: **http://127.0.0.1:8000**

### Reset Data (Simulasi 7 Hari)

```bash
php artisan migrate:fresh --seed
# Otomatis generate: pembelian, penjualan, closing kasir, absensi, arus kas
```

---

## 🔑 Akun Default

| Role | Username | Password | Akses |
|------|----------|----------|-------|
| Admin | `admin` | `Admin123!` | Semua fitur + pengaturan + user management |
| Apoteker | `apoteker` | `Apoteker123!` | POS, stok, pembelian, laporan |
| Asisten | `asisten` | `Asisten123!` | POS, stok (terbatas) |

---

## ⌨️ Keyboard Shortcuts (POS)

| Tombol | Fungsi |
|--------|--------|
| `F1` | Focus pencarian barang |
| `F2` | Focus input jumlah bayar |
| `F5` | Transaksi baru (reset) |
| `F8` | Proses transaksi |
| `Esc` | Kosongkan keranjang |

---

## 📊 Sistem Shift & Closing

| Shift | Jam | Keterangan |
|-------|-----|-----------|
| Pagi | 07:00 – 13:59 | Shift pertama |
| Siang | 14:00 – 21:00 | Shift kedua |

**Flow Closing:**
1. Buka Shift → input saldo awal
2. Transaksi POS + input pengeluaran operasional
3. Tutup Shift → auto-hitung saldo akhir

```
Saldo Akhir = Saldo Awal + Penjualan Tunai - Pengeluaran
(Non-tunai langsung ke bank, tidak masuk saldo kasir)
```

---

## 💰 Rumus Harga

```
Harga Jual  = HNA × (1 + PPN%)           → default: HNA × 1.10
Harga HV    = Harga Jual × (1 + Markup HV%)   → default: Harga Jual × 1.10
Harga Resep = Harga HV × (1 + Markup Resep%)  → default: Harga HV × 1.08
```

> Semua persentase bisa diubah di menu **Pengaturan** (admin only).

---

## 🗂️ Struktur Proyek

```
app/
├── Http/Controllers/
│   ├── Auth/LoginController.php
│   ├── DashboardController.php
│   ├── ProductController.php
│   ├── CategoryController.php
│   ├── SupplierController.php
│   ├── PurchaseController.php
│   ├── SaleController.php           ← POS + autocomplete pasien
│   ├── ReportController.php         ← Laporan + closing + pengeluaran
│   ├── AttendanceController.php
│   ├── StockOpnameController.php
│   ├── UserController.php
│   ├── SettingController.php        ← Pengaturan harga
│   └── AuditLogController.php       ← Log aktivitas
├── Models/ (20 models)
├── Middleware/
│   ├── RoleMiddleware.php
│   └── SecurityHeaders.php
database/
├── migrations/ (14 files)
├── seeders/
│   ├── DatabaseSeeder.php
│   └── SimulationSeeder.php         ← simulasi 7 hari
resources/views/
├── layouts/ (app, sidebar, navbar)
├── pos/index.blade.php              ← POS kasir
├── products/, suppliers/, purchases/, sales/
├── reports/ (sales, closing-kasir, cash-flow, top-products)
├── stock-opname/, categories/, users/, attendance/
└── manual.blade.php                 ← Buku panduan
routes/
└── web.php                          ← Semua routes
```

---

## 🛣️ API Endpoints

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| GET | `/api/products/search` | Search produk (barcode/kode/nama) |
| POST | `/api/products/hitung-harga` | Kalkulasi harga otomatis |
| GET | `/api/pasien/search` | Autocomplete data pasien |
| GET | `/api/settings/pricing` | Get konfigurasi harga (PPN, markup) |

---

## 🧪 Data Simulasi

Seeder otomatis generate data realistis 7 hari:
- 📦 Pembelian dari supplier (multi faktur)
- 🛒 Penjualan reguler & resep
- 💵 Closing kasir per shift
- ⏰ Absensi karyawan
- 💰 Arus kas (debit/kredit)
- 📊 Stok card movements

---

## 📋 Database Schema

14 tabel utama:

| Tabel | Keterangan |
|-------|-----------|
| `users` | User + role (admin/apoteker/asisten_apoteker) |
| `suppliers` | Data PBF/supplier |
| `categories` | Kategori + kelas terapi |
| `products` | Master barang (stok, harga, grup, expired) |
| `stock_cards` | Kartu stok (masuk/keluar/opname/retur) |
| `stock_opnames` + `details` | Stock opname per periode |
| `purchases` + `details` | Pembelian (faktur, item, diskon) |
| `purchase_returns` + `details` | Retur pembelian |
| `customers` | Data pelanggan |
| `sales` + `details` | Penjualan (nota, item, pasien) |
| `cash_registers` | Closing kasir per shift |
| `cash_flows` | Arus kas (debit/kredit) |
| `attendances` | Absensi karyawan |
| `price_histories` | Riwayat perubahan harga |
| `audit_logs` | Log aktivitas sistem |
| `settings` | Konfigurasi aplikasi |

---

## 🐛 Known Issues

- Export Excel belum tersedia (butuh maatwebsite/excel)
- Lazy loading / infinite scroll belum diimplementasi

---

## 📝 License

MIT License

---

## 👨‍💻 Development

```bash
# Development mode (hot reload)
npm run dev

# Production build
npm run build

# Fresh migration + seed
php artisan migrate:fresh --seed
```
