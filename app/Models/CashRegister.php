<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'shift',
        'user_id',
        'saldo_awal',
        'total_penjualan_tunai',
        'total_penjualan_non_tunai',
        'total_penjualan_hv',
        'total_penjualan_resep',
        'pengeluaran',
        'saldo_akhir',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'saldo_awal' => 'decimal:2',
        'total_penjualan_tunai' => 'decimal:2',
        'total_penjualan_non_tunai' => 'decimal:2',
        'total_penjualan_hv' => 'decimal:2',
        'total_penjualan_resep' => 'decimal:2',
        'pengeluaran' => 'decimal:2',
        'saldo_akhir' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
