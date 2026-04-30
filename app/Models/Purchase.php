<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_faktur',
        'tanggal_faktur',
        'tanggal_jatuh_tempo',
        'supplier_id',
        'subtotal',
        'diskon_total',
        'ppn',
        'grand_total',
        'status',
        'status_bayar',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_faktur' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'subtotal' => 'decimal:2',
        'diskon_total' => 'decimal:2',
        'ppn' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function returns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
