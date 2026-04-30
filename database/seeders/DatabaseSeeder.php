<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Default Settings
        Setting::create(['key' => 'ppn_persen', 'value' => '10', 'label' => 'PPN (%)', 'group' => 'pricing']);
        Setting::create(['key' => 'markup_hv_persen', 'value' => '10', 'label' => 'Markup HV (%)', 'group' => 'pricing']);
        Setting::create(['key' => 'markup_resep_persen', 'value' => '8', 'label' => 'Markup Resep (%)', 'group' => 'pricing']);

        // Create Users
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@apotek.com',
            'username' => 'admin',
            'password' => Hash::make('Admin123!'),
            'role' => 'admin',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Dr. Apoteker',
            'email' => 'apoteker@apotek.com',
            'username' => 'apoteker',
            'password' => Hash::make('Apoteker123!'),
            'role' => 'apoteker',
            'phone' => '081234567891',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Asisten Apoteker',
            'email' => 'asisten@apotek.com',
            'username' => 'asisten',
            'password' => Hash::make('Asisten123!'),
            'role' => 'asisten_apoteker',
            'phone' => '081234567892',
            'is_active' => true,
        ]);

        // Create Categories
        $categories = [
            ['nama' => 'Analgesik & Antipiretik', 'kelas_terapi' => 'Analgesik'],
            ['nama' => 'Antibiotik', 'kelas_terapi' => 'Anti Infeksi'],
            ['nama' => 'Antihipertensi', 'kelas_terapi' => 'Kardiovaskular'],
            ['nama' => 'Antidiabetes', 'kelas_terapi' => 'Endokrin'],
            ['nama' => 'Vitamin & Suplemen', 'kelas_terapi' => 'Suplemen'],
            ['nama' => 'Obat Batuk & Flu', 'kelas_terapi' => 'Respirasi'],
            ['nama' => 'Obat Maag & Pencernaan', 'kelas_terapi' => 'Gastrointestinal'],
            ['nama' => 'Obat Kulit', 'kelas_terapi' => 'Dermatologi'],
            ['nama' => 'Obat Mata & Telinga', 'kelas_terapi' => 'Oftalmologi'],
            ['nama' => 'Alat Kesehatan', 'kelas_terapi' => 'Alkes'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Create Suppliers
        $suppliers = [
            ['kode' => 'PBF-001', 'nama' => 'PT. Kimia Farma Trading', 'kota' => 'Jakarta', 'no_telp' => '021-1234567', 'jatuh_tempo' => 30],
            ['kode' => 'PBF-002', 'nama' => 'PT. Anugrah Pharmindo Lestari', 'kota' => 'Bandung', 'no_telp' => '022-7654321', 'jatuh_tempo' => 45],
            ['kode' => 'PBF-003', 'nama' => 'PT. Enseval Putera Megatrading', 'kota' => 'Jakarta', 'no_telp' => '021-9876543', 'jatuh_tempo' => 30],
            ['kode' => 'PBF-004', 'nama' => 'PT. Millennium Pharmacon International', 'kota' => 'Surabaya', 'no_telp' => '031-5551234', 'jatuh_tempo' => 60],
            ['kode' => 'PBF-005', 'nama' => 'PT. Dos Ni Roha', 'kota' => 'Jakarta', 'no_telp' => '021-4443333', 'jatuh_tempo' => 30],
        ];

        foreach ($suppliers as $sup) {
            Supplier::create($sup);
        }

        // Create Products
        $products = [
            ['kode_barang' => 'OBT-001', 'barcode' => '8991234560001', 'nama_barang' => 'Paracetamol 500mg', 'category_id' => 1, 'satuan' => 'strip', 'pabrik' => 'Kimia Farma', 'grup' => 'hijau', 'stok' => 150, 'stok_minimum' => 20, 'harga_beli' => 3500],
            ['kode_barang' => 'OBT-002', 'barcode' => '8991234560002', 'nama_barang' => 'Amoxicillin 500mg', 'category_id' => 2, 'satuan' => 'strip', 'pabrik' => 'Sanbe Farma', 'grup' => 'merah', 'stok' => 80, 'stok_minimum' => 10, 'harga_beli' => 8500],
            ['kode_barang' => 'OBT-003', 'barcode' => '8991234560003', 'nama_barang' => 'Amlodipine 5mg', 'category_id' => 3, 'satuan' => 'strip', 'pabrik' => 'Dexa Medica', 'grup' => 'merah', 'stok' => 60, 'stok_minimum' => 10, 'harga_beli' => 12000],
            ['kode_barang' => 'OBT-004', 'barcode' => '8991234560004', 'nama_barang' => 'Metformin 500mg', 'category_id' => 4, 'satuan' => 'strip', 'pabrik' => 'Merck', 'grup' => 'merah', 'stok' => 45, 'stok_minimum' => 10, 'harga_beli' => 6500],
            ['kode_barang' => 'OBT-005', 'barcode' => '8991234560005', 'nama_barang' => 'Vitamin C 1000mg', 'category_id' => 5, 'satuan' => 'strip', 'pabrik' => 'Sido Muncul', 'grup' => 'hijau', 'stok' => 200, 'stok_minimum' => 30, 'harga_beli' => 15000],
            ['kode_barang' => 'OBT-006', 'barcode' => '8991234560006', 'nama_barang' => 'OBH Combi Batuk', 'category_id' => 6, 'satuan' => 'botol', 'pabrik' => 'Combiphar', 'grup' => 'hijau', 'stok' => 35, 'stok_minimum' => 10, 'harga_beli' => 18000],
            ['kode_barang' => 'OBT-007', 'barcode' => '8991234560007', 'nama_barang' => 'Antasida DOEN', 'category_id' => 7, 'satuan' => 'botol', 'pabrik' => 'Kimia Farma', 'grup' => 'hijau', 'stok' => 40, 'stok_minimum' => 10, 'harga_beli' => 8000],
            ['kode_barang' => 'OBT-008', 'barcode' => '8991234560008', 'nama_barang' => 'Salep 88 (Ketoconazole)', 'category_id' => 8, 'satuan' => 'tube', 'pabrik' => 'Surya Dermato', 'grup' => 'hijau', 'stok' => 25, 'stok_minimum' => 5, 'harga_beli' => 12000],
            ['kode_barang' => 'OBT-009', 'barcode' => '8991234560009', 'nama_barang' => 'Cendo Xitrol Tetes Mata', 'category_id' => 9, 'satuan' => 'botol', 'pabrik' => 'Cendo', 'grup' => 'merah', 'stok' => 15, 'stok_minimum' => 5, 'harga_beli' => 35000],
            ['kode_barang' => 'OBT-010', 'barcode' => '8991234560010', 'nama_barang' => 'Masker Medis 3 Ply', 'category_id' => 10, 'satuan' => 'box', 'pabrik' => 'Sensi', 'grup' => 'hijau', 'stok' => 50, 'stok_minimum' => 10, 'harga_beli' => 25000],
            ['kode_barang' => 'OBT-011', 'barcode' => '8991234560011', 'nama_barang' => 'Ibuprofen 400mg', 'category_id' => 1, 'satuan' => 'strip', 'pabrik' => 'Tempo Scan', 'grup' => 'hijau', 'stok' => 3, 'stok_minimum' => 10, 'harga_beli' => 5000],
            ['kode_barang' => 'OBT-012', 'barcode' => '8991234560012', 'nama_barang' => 'Ciprofloxacin 500mg', 'category_id' => 2, 'satuan' => 'strip', 'pabrik' => 'Indofarma', 'grup' => 'merah', 'stok' => 2, 'stok_minimum' => 5, 'harga_beli' => 9500],
            ['kode_barang' => 'OBT-013', 'barcode' => '8991234560013', 'nama_barang' => 'Omeprazole 20mg', 'category_id' => 7, 'satuan' => 'strip', 'pabrik' => 'Kalbe Farma', 'grup' => 'merah', 'stok' => 4, 'stok_minimum' => 10, 'harga_beli' => 7500],
            ['kode_barang' => 'OBT-014', 'barcode' => '8991234560014', 'nama_barang' => 'Cetirizine 10mg', 'category_id' => 6, 'satuan' => 'strip', 'pabrik' => 'Hexpharm', 'grup' => 'merah', 'stok' => 30, 'stok_minimum' => 10, 'harga_beli' => 4500],
            ['kode_barang' => 'OBT-015', 'barcode' => '8991234560015', 'nama_barang' => 'Multivitamin Enervon-C', 'category_id' => 5, 'satuan' => 'strip', 'pabrik' => 'Darya Varia', 'grup' => 'hijau', 'stok' => 100, 'stok_minimum' => 20, 'harga_beli' => 11000],
        ];

        foreach ($products as $p) {
            $harga = Product::hitungHarga($p['harga_beli']);
            Product::create(array_merge($p, $harga));
        }

        // Create Customers
        Customer::create(['nama' => 'Umum', 'tipe' => 'umum']);
        Customer::create(['nama' => 'Budi Santoso', 'no_telp' => '081111222333', 'tipe' => 'langganan']);
        Customer::create(['nama' => 'Siti Rahayu', 'no_telp' => '082222333444', 'tipe' => 'langganan']);
        Customer::create(['nama' => 'Pasien Resep', 'tipe' => 'resep']);
        Customer::create(['nama' => 'Ibu Kartini', 'no_telp' => '083333444555', 'tipe' => 'langganan']);
        Customer::create(['nama' => 'Pak Joko', 'no_telp' => '084444555666', 'tipe' => 'langganan']);
        Customer::create(['nama' => 'Ny. Aminah', 'no_telp' => '085555666777', 'tipe' => 'resep']);

        // Jalankan simulasi 7 hari
        $this->call(SimulationSeeder::class);
    }
}
