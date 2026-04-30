<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('no_faktur')->unique();
            $table->date('tanggal_faktur');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('diskon_total', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->enum('status', ['pending', 'completed', 'returned'])->default('pending');
            $table->enum('status_bayar', ['belum_bayar', 'sebagian', 'lunas'])->default('belum_bayar');
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('jumlah');
            $table->decimal('harga_beli', 12, 2);
            $table->decimal('diskon_persen', 5, 2)->default(0);
            $table->decimal('diskon_nominal', 12, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->date('expired_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
        Schema::dropIfExists('purchases');
    }
};
