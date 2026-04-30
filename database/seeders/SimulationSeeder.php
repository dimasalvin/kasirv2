<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\StockCard;
use App\Models\CashRegister;
use App\Models\CashFlow;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SimulationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏥 Memulai simulasi apotek 7 hari...');

        $startDate = Carbon::today()->subDays(6); // 7 hari terakhir termasuk hari ini
        $users = User::all();
        $products = Product::all();
        $suppliers = Supplier::all();
        $customers = Customer::all();

        // ===== SIMULASI PER HARI =====
        for ($day = 0; $day < 7; $day++) {
            $date = $startDate->copy()->addDays($day);
            $this->command->info("📅 Hari ke-" . ($day + 1) . ": " . $date->format('d/m/Y (l)'));

            // --- ABSENSI ---
            $this->seedAttendance($date, $users);

            // --- PEMBELIAN (2-3x per minggu) ---
            if (in_array($day, [0, 2, 4])) {
                $this->seedPurchases($date, $products, $suppliers, $users);
            }

            // --- PENJUALAN (per shift) ---
            $this->seedSales($date, $products, $customers, $users, $day);

            // --- CLOSING KASIR ---
            $this->seedCashRegister($date, $users);
        }

        // --- SIMULASI AUDIT LOG ---
        $this->seedAuditLogs($startDate, $users);

        $this->command->info('✅ Simulasi selesai! Data 7 hari berhasil dibuat.');
    }

    private function seedAttendance(Carbon $date, $users): void
    {
        foreach ($users as $user) {
            // 90% hadir, 10% izin/sakit
            $status = (rand(1, 10) <= 9) ? 'hadir' : (rand(0, 1) ? 'izin' : 'sakit');

            $jamMasuk = null;
            $jamPulang = null;

            if ($status === 'hadir') {
                $jamMasuk = sprintf('%02d:%02d:00', rand(7, 8), rand(0, 30));
                $jamPulang = sprintf('%02d:%02d:00', rand(16, 17), rand(0, 59));
            }

            Attendance::create([
                'user_id' => $user->id,
                'tanggal' => $date,
                'jam_masuk' => $jamMasuk,
                'jam_pulang' => $jamPulang,
                'status' => $status,
                'keterangan' => $status !== 'hadir' ? 'Simulasi data' : null,
            ]);
        }
    }

    private function seedPurchases(Carbon $date, $products, $suppliers, $users): void
    {
        // 1-2 faktur per hari pembelian
        $numPurchases = rand(1, 2);

        for ($p = 0; $p < $numPurchases; $p++) {
            $supplier = $suppliers->random();
            $user = $users->where('role', '!=', 'asisten_apoteker')->random();
            $noFaktur = 'FKT-' . $date->format('Ymd') . '-' . str_pad($p + 1, 3, '0', STR_PAD_LEFT);

            $purchase = Purchase::create([
                'no_faktur' => $noFaktur,
                'tanggal_faktur' => $date,
                'tanggal_jatuh_tempo' => $date->copy()->addDays($supplier->jatuh_tempo),
                'supplier_id' => $supplier->id,
                'status' => 'completed',
                'status_bayar' => rand(0, 1) ? 'lunas' : 'belum_bayar',
                'catatan' => null,
                'user_id' => $user->id,
            ]);

            // 3-6 item per faktur
            $numItems = rand(3, 6);
            $selectedProducts = $products->random($numItems);
            $subtotal = 0;
            $diskonTotal = 0;

            foreach ($selectedProducts as $product) {
                $jumlah = rand(10, 50);
                // Variasi harga beli ±5% untuk simulasi perubahan harga supplier
                $variasi = rand(-5, 5) / 100; // -5% sampai +5%
                $hargaBeli = round($product->harga_beli * (1 + $variasi));
                $diskonPersen = rand(0, 3) * 2.5; // 0%, 2.5%, 5%, 7.5%
                $gross = $hargaBeli * $jumlah;
                $diskonNominal = $gross * ($diskonPersen / 100);
                $itemSubtotal = $gross - $diskonNominal;

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'jumlah' => $jumlah,
                    'harga_beli' => $hargaBeli,
                    'diskon_persen' => $diskonPersen,
                    'diskon_nominal' => $diskonNominal,
                    'subtotal' => $itemSubtotal,
                    'expired_date' => $date->copy()->addMonths(rand(12, 36)),
                    'batch_number' => 'B' . $date->format('Ymd') . rand(100, 999),
                ]);

                // Update stok & harga
                $stokSebelum = $product->stok;
                $hargaBeliLama = (float) $product->harga_beli;
                $product->increment('stok', $jumlah);

                // Update harga jika berubah & catat riwayat
                if ($hargaBeli != $hargaBeliLama) {
                    $hargaJualLama = (float) $product->harga_jual;
                    $hargaBaru = Product::hitungHarga($hargaBeli);
                    $product->update(array_merge(['harga_beli' => $hargaBeli], $hargaBaru));

                    \App\Models\PriceHistory::create([
                        'product_id' => $product->id,
                        'harga_beli_lama' => $hargaBeliLama,
                        'harga_beli_baru' => $hargaBeli,
                        'harga_jual_lama' => $hargaJualLama,
                        'harga_jual_baru' => $hargaBaru['harga_jual'],
                        'referensi' => $noFaktur,
                        'user_id' => $user->id,
                        'created_at' => $date->copy()->setTime(rand(8, 10), rand(0, 59)),
                        'updated_at' => $date->copy()->setTime(rand(8, 10), rand(0, 59)),
                    ]);
                }

                StockCard::create([
                    'product_id' => $product->id,
                    'tipe' => 'masuk',
                    'jumlah' => $jumlah,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSebelum + $jumlah,
                    'referensi' => $noFaktur,
                    'keterangan' => "Pembelian dari {$supplier->nama}",
                    'user_id' => $user->id,
                    'created_at' => $date->copy()->setTime(rand(8, 10), rand(0, 59)),
                    'updated_at' => $date->copy()->setTime(rand(8, 10), rand(0, 59)),
                ]);

                $subtotal += $gross;
                $diskonTotal += $diskonNominal;
            }

            $ppn = ($subtotal - $diskonTotal) * 0.10;
            $grandTotal = $subtotal - $diskonTotal + $ppn;

            $purchase->update([
                'subtotal' => $subtotal,
                'diskon_total' => $diskonTotal,
                'ppn' => $ppn,
                'grand_total' => $grandTotal,
            ]);

            CashFlow::create([
                'tanggal' => $date,
                'tipe' => 'kredit',
                'nominal' => $grandTotal,
                'keterangan' => "Pembelian {$noFaktur} - {$supplier->nama}",
                'referensi' => $noFaktur,
                'user_id' => $user->id,
                'created_at' => $date->copy()->setTime(rand(8, 10), rand(0, 59)),
                'updated_at' => $date->copy()->setTime(rand(8, 10), rand(0, 59)),
            ]);
        }
    }

    private function seedSales(Carbon $date, $products, $customers, $users, int $dayIndex): void
    {
        // Penjualan per shift
        $shifts = [
            'pagi' => ['start' => 7, 'end' => 13, 'min_trx' => 8, 'max_trx' => 18],
            'siang' => ['start' => 14, 'end' => 21, 'min_trx' => 10, 'max_trx' => 22],
        ];

        // Weekend lebih ramai
        $isWeekend = in_array($date->dayOfWeek, [0, 6]);
        $multiplier = $isWeekend ? 1.3 : 1.0;

        $dailyCounter = 1;

        foreach ($shifts as $shift => $config) {
            $numTransactions = rand(
                (int)($config['min_trx'] * $multiplier),
                (int)($config['max_trx'] * $multiplier)
            );

            $kasir = $users->random();

            for ($t = 0; $t < $numTransactions; $t++) {
                // Refresh stok dari database setiap transaksi
                $freshProducts = Product::where('is_active', true)->where('stok', '>', 0)->get();

                // Skip jika tidak ada produk tersedia
                if ($freshProducts->isEmpty()) {
                    continue;
                }

                $hour = rand($config['start'], $config['end'] - 1);
                $minute = rand(0, 59);
                $second = rand(0, 59);
                $jam = sprintf('%02d:%02d:%02d', $hour, $minute, $second);

                // 30% resep, 70% reguler
                $isResep = rand(1, 10) <= 3;
                $tipePenjualan = $isResep ? 'resep' : 'reguler';

                // 60% tunai, 40% non tunai
                $metodeBayar = rand(1, 10) <= 6 ? 'tunai' : 'non_tunai';

                $noNota = 'INV-' . $date->format('Ymd') . '-' . str_pad($dailyCounter, 4, '0', STR_PAD_LEFT);
                $dailyCounter++;

                // Pilih customer
                $customer = rand(1, 10) <= 4 ? $customers->random() : null;

                // 1-4 item per transaksi (max sesuai produk tersedia)
                $numItems = rand(1, min(4, $freshProducts->count()));
                $selectedProducts = $freshProducts->random($numItems);

                $subtotal = 0;
                $diskonTotal = 0;
                $details = [];

                foreach ($selectedProducts as $product) {
                    $maxQty = min(3, $product->stok); // Max 3 per item agar stok tidak cepat habis
                    if ($maxQty < 1) continue;

                    $jumlah = rand(1, $maxQty);
                    $tipeHarga = $isResep ? 'resep' : 'hv';
                    $hargaSatuan = $tipeHarga === 'resep' ? (float)$product->harga_resep : (float)$product->harga_hv;

                    // 20% chance diskon per item
                    $diskonPersen = rand(1, 10) <= 2 ? rand(1, 2) * 5 : 0; // 5% atau 10%
                    $gross = $hargaSatuan * $jumlah;
                    $diskonNominal = round($gross * ($diskonPersen / 100));
                    $itemSubtotal = round($gross - $diskonNominal);

                    $details[] = [
                        'product_id' => $product->id,
                        'jumlah' => $jumlah,
                        'harga_satuan' => $hargaSatuan,
                        'diskon_persen' => $diskonPersen,
                        'diskon_nominal' => $diskonNominal,
                        'subtotal' => $itemSubtotal,
                        'tipe_harga' => $tipeHarga,
                    ];

                    // Kurangi stok langsung di DB
                    Product::where('id', $product->id)->decrement('stok', $jumlah);

                    $subtotal += round($gross);
                    $diskonTotal += $diskonNominal;
                }

                // Skip jika tidak ada detail (semua produk stok 0)
                if (empty($details)) {
                    $dailyCounter--;
                    continue;
                }

                $grandTotal = round($subtotal - $diskonTotal);
                $bayar = $metodeBayar === 'tunai'
                    ? (int)(ceil($grandTotal / 5000) * 5000)
                    : $grandTotal;
                $kembalian = max(0, $bayar - $grandTotal);

                // Data pasien untuk resep
                $pasienData = $isResep ? $this->randomPasien() : ['nama' => null, 'no_hp' => null, 'alamat' => null];

                // Buat sale dengan total yang sudah dihitung
                $sale = Sale::create([
                    'no_nota' => $noNota,
                    'tanggal' => $date,
                    'jam' => $jam,
                    'customer_id' => $customer?->id,
                    'user_id' => $kasir->id,
                    'tipe_penjualan' => $tipePenjualan,
                    'shift' => $shift,
                    'metode_bayar' => $metodeBayar,
                    'referensi_bayar' => $metodeBayar === 'non_tunai' ? 'EDC-' . rand(100000, 999999) : null,
                    'status' => 'completed',
                    'nama_dokter' => null,
                    'pasien_nama' => $pasienData['nama'],
                    'pasien_no_hp' => $pasienData['no_hp'],
                    'pasien_alamat' => $pasienData['alamat'],
                    'subtotal' => $subtotal,
                    'diskon_total' => $diskonTotal,
                    'grand_total' => $grandTotal,
                    'bayar' => $bayar,
                    'kembalian' => $kembalian,
                    'created_at' => $date->copy()->setTime($hour, $minute, $second),
                    'updated_at' => $date->copy()->setTime($hour, $minute, $second),
                ]);

                // Buat detail & stock cards
                foreach ($details as $detail) {
                    SaleDetail::create(array_merge($detail, ['sale_id' => $sale->id]));

                    $currentStok = Product::find($detail['product_id'])->stok;
                    StockCard::create([
                        'product_id' => $detail['product_id'],
                        'tipe' => 'keluar',
                        'jumlah' => $detail['jumlah'],
                        'stok_sebelum' => $currentStok + $detail['jumlah'],
                        'stok_sesudah' => $currentStok,
                        'referensi' => $noNota,
                        'keterangan' => 'Penjualan',
                        'user_id' => $kasir->id,
                        'created_at' => $date->copy()->setTime($hour, $minute, $second),
                        'updated_at' => $date->copy()->setTime($hour, $minute, $second),
                    ]);
                }

                // Cash flow
                CashFlow::create([
                    'tanggal' => $date,
                    'tipe' => 'debit',
                    'nominal' => $grandTotal,
                    'keterangan' => "Penjualan {$noNota}",
                    'referensi' => $noNota,
                    'user_id' => $kasir->id,
                    'created_at' => $date->copy()->setTime($hour, $minute, $second),
                    'updated_at' => $date->copy()->setTime($hour, $minute, $second),
                ]);
            }
        }
    }

    private function seedCashRegister(Carbon $date, $users): void
    {
        $shifts = ['pagi', 'siang'];

        foreach ($shifts as $shift) {
            $kasir = $users->random();

            $sales = Sale::where('tanggal', $date)
                ->where('shift', $shift)
                ->where('status', 'completed')
                ->get();

            $totalTunai = $sales->where('metode_bayar', 'tunai')->sum('grand_total');
            $totalNonTunai = $sales->where('metode_bayar', 'non_tunai')->sum('grand_total');
            $totalHV = $sales->where('tipe_penjualan', 'reguler')->sum('grand_total');
            $totalResep = $sales->where('tipe_penjualan', 'resep')->sum('grand_total');

            $saldoAwal = rand(2, 5) * 100000; // 200K - 500K
            $pengeluaran = rand(0, 2) * 50000; // 0 - 100K pengeluaran kecil
            $saldoAkhir = $saldoAwal + $totalTunai - $pengeluaran;

            CashRegister::create([
                'tanggal' => $date,
                'shift' => $shift,
                'user_id' => $kasir->id,
                'saldo_awal' => $saldoAwal,
                'total_penjualan_tunai' => $totalTunai,
                'total_penjualan_non_tunai' => $totalNonTunai,
                'total_penjualan_hv' => $totalHV,
                'total_penjualan_resep' => $totalResep,
                'pengeluaran' => $pengeluaran,
                'saldo_akhir' => $saldoAkhir,
                'status' => 'closed',
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            // Pengeluaran kecil
            if ($pengeluaran > 0) {
                CashFlow::create([
                    'tanggal' => $date,
                    'tipe' => 'kredit',
                    'nominal' => $pengeluaran,
                    'keterangan' => "Pengeluaran operasional shift {$shift}",
                    'referensi' => "OPS-{$date->format('Ymd')}-{$shift}",
                    'user_id' => $kasir->id,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }

    private function randomPasien(): array
    {
        $pasiens = [
            ['nama' => 'Ahmad Fauzi', 'no_hp' => '081234567001', 'alamat' => 'Jl. Merdeka No. 12, Bandung'],
            ['nama' => 'Siti Aminah', 'no_hp' => '081234567002', 'alamat' => 'Jl. Sudirman No. 45, Jakarta'],
            ['nama' => 'Budi Santoso', 'no_hp' => '081234567003', 'alamat' => 'Jl. Gatot Subroto No. 8, Surabaya'],
            ['nama' => 'Dewi Kartini', 'no_hp' => '081234567004', 'alamat' => 'Jl. Diponegoro No. 23, Semarang'],
            ['nama' => 'Rudi Hermawan', 'no_hp' => '081234567005', 'alamat' => 'Jl. Ahmad Yani No. 56, Malang'],
            ['nama' => 'Maya Sari', 'no_hp' => '081234567006', 'alamat' => 'Jl. Pahlawan No. 9, Yogyakarta'],
            ['nama' => 'Hendra Wijaya', 'no_hp' => '081234567007', 'alamat' => 'Jl. Veteran No. 34, Bogor'],
            ['nama' => 'Ratna Dewi', 'no_hp' => '081234567008', 'alamat' => 'Jl. Cendana No. 17, Depok'],
            ['nama' => 'Agus Setiawan', 'no_hp' => '081234567009', 'alamat' => 'Jl. Melati No. 5, Tangerang'],
            ['nama' => 'Rina Wulandari', 'no_hp' => '081234567010', 'alamat' => 'Jl. Kenanga No. 28, Bekasi'],
        ];
        return $pasiens[array_rand($pasiens)];
    }

    private function seedAuditLogs(Carbon $startDate, $users): void
    {
        $logs = [
            ['action' => 'login', 'desc' => 'User login: Administrator'],
            ['action' => 'login', 'desc' => 'User login: Dr. Apoteker'],
            ['action' => 'login', 'desc' => 'User login: Asisten Apoteker'],
            ['action' => 'create', 'desc' => 'Tambah barang: Dexamethasone 0.5mg (OBT-016)'],
            ['action' => 'update', 'desc' => 'Edit barang: Paracetamol 500mg (OBT-001) - update stok minimum'],
            ['action' => 'create', 'desc' => 'Tambah supplier: PT. Pharma Baru (PBF-006)'],
            ['action' => 'update', 'desc' => 'Edit supplier: PT. Kimia Farma Trading - update jatuh tempo'],
            ['action' => 'create', 'desc' => 'Pembelian FKT-20260428-001 - PT. Kimia Farma - Rp 2,500,000'],
            ['action' => 'void', 'desc' => 'Void nota INV-20260427-0005 - Rp 45,000 (salah input)'],
            ['action' => 'update', 'desc' => 'Pengaturan harga diubah: PPN 10% → 11%'],
            ['action' => 'update', 'desc' => 'Pengaturan harga diubah: PPN 11% → 10% (rollback)'],
            ['action' => 'create', 'desc' => 'Tambah user: Kasir Baru (asisten_apoteker)'],
            ['action' => 'delete', 'desc' => 'Hapus user: Kasir Baru'],
            ['action' => 'logout', 'desc' => 'User logout: Administrator'],
            ['action' => 'login', 'desc' => 'User login: Dr. Apoteker'],
            ['action' => 'logout', 'desc' => 'User logout: Dr. Apoteker'],
        ];

        foreach ($logs as $i => $log) {
            $date = $startDate->copy()->addDays(rand(0, 6));
            $hour = rand(7, 22);
            $minute = rand(0, 59);

            \App\Models\AuditLog::create([
                'user_id' => $users->random()->id,
                'action' => $log['action'],
                'description' => $log['desc'],
                'ip_address' => '127.0.0.1',
                'created_at' => $date->copy()->setTime($hour, $minute, rand(0, 59)),
                'updated_at' => $date->copy()->setTime($hour, $minute, rand(0, 59)),
            ]);
        }
    }
}
