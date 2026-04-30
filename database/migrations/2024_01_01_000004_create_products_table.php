<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('barcode')->nullable()->index();
            $table->string('nama_barang');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('satuan')->default('pcs'); // pcs, strip, box, botol, tube
            $table->string('pabrik')->nullable();
            $table->enum('grup', ['hijau', 'merah', 'biru'])->default('hijau');
            // hijau = obat bebas, merah = obat keras/narkotika, biru = konsinyasi
            $table->string('kelas_terapi')->nullable();
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(5);
            $table->decimal('harga_beli', 12, 2)->default(0); // HNA
            $table->decimal('harga_jual', 12, 2)->default(0); // include PPN
            $table->decimal('harga_hv', 12, 2)->default(0); // +10% dari harga jual
            $table->decimal('harga_resep', 12, 2)->default(0); // +8% dari HV
            $table->date('expired_date')->nullable();
            $table->string('lokasi_rak')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
