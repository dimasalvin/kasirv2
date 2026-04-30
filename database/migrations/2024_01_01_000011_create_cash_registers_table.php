<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('shift', ['pagi', 'siang']);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->decimal('total_penjualan_tunai', 15, 2)->default(0);
            $table->decimal('total_penjualan_non_tunai', 15, 2)->default(0);
            $table->decimal('total_penjualan_hv', 15, 2)->default(0);
            $table->decimal('total_penjualan_resep', 15, 2)->default(0);
            $table->decimal('pengeluaran', 15, 2)->default(0);
            $table->decimal('saldo_akhir', 15, 2)->default(0);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
