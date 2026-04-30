<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Ganti nama_dokter dengan data pasien resep
            $table->string('pasien_nama')->nullable()->after('nama_dokter');
            $table->string('pasien_no_hp')->nullable()->after('pasien_nama');
            $table->text('pasien_alamat')->nullable()->after('pasien_no_hp');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['pasien_nama', 'pasien_no_hp', 'pasien_alamat']);
        });
    }
};
