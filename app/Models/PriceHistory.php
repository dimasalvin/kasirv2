<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $fillable = [
        'product_id', 'harga_beli_lama', 'harga_beli_baru',
        'harga_jual_lama', 'harga_jual_baru', 'referensi', 'user_id',
    ];

    public function product() { return $this->belongsTo(Product::class); }
    public function user() { return $this->belongsTo(User::class); }
}
