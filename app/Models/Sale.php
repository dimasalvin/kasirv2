<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_nota',
        'tanggal',
        'jam',
        'customer_id',
        'user_id',
        'tipe_penjualan',
        'shift',
        'subtotal',
        'diskon_total',
        'grand_total',
        'bayar',
        'kembalian',
        'metode_bayar',
        'referensi_bayar',
        'status',
        'catatan',
        'nama_dokter',
        'pasien_nama',
        'pasien_no_hp',
        'pasien_alamat',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'subtotal' => 'decimal:2',
        'diskon_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    // Generate nomor nota
    public static function generateNoNota(): string
    {
        $today = now()->format('Ymd');
        $lastSale = self::where('no_nota', 'like', "INV-{$today}%")
            ->orderBy('no_nota', 'desc')
            ->first();

        if ($lastSale) {
            $lastNumber = (int) substr($lastSale->no_nota, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "INV-{$today}-" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
