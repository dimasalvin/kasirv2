<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('no_nota')->unique();
            $table->date('tanggal');
            $table->time('jam');
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('tipe_penjualan', ['reguler', 'resep'])->default('reguler');
            $table->enum('shift', ['pagi', 'siang'])->default('pagi');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('diskon_total', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('bayar', 15, 2)->default(0);
            $table->decimal('kembalian', 15, 2)->default(0);
            $table->enum('metode_bayar', ['tunai', 'non_tunai'])->default('tunai');
            $table->string('referensi_bayar')->nullable(); // no EDC / bank
            $table->enum('status', ['completed', 'void'])->default('completed');
            $table->text('catatan')->nullable();
            $table->string('nama_dokter')->nullable(); // untuk resep
            $table->timestamps();
        });

        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('diskon_persen', 5, 2)->default(0);
            $table->decimal('diskon_nominal', 12, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->enum('tipe_harga', ['hv', 'resep'])->default('hv');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_details');
        Schema::dropIfExists('sales');
    }
};
