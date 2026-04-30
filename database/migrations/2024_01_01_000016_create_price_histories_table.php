<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('harga_beli_lama', 12, 2);
            $table->decimal('harga_beli_baru', 12, 2);
            $table->decimal('harga_jual_lama', 12, 2);
            $table->decimal('harga_jual_baru', 12, 2);
            $table->string('referensi')->nullable(); // no faktur pembelian
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_histories');
    }
};
