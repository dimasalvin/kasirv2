<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'category_id',
        'satuan',
        'pabrik',
        'grup',
        'kelas_terapi',
        'stok',
        'stok_minimum',
        'harga_beli',
        'harga_jual',
        'harga_hv',
        'harga_resep',
        'lokasi_rak',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'harga_hv' => 'decimal:2',
        'harga_resep' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockCards()
    {
        return $this->hasMany(StockCard::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function priceHistories()
    {
        return $this->hasMany(PriceHistory::class);
    }

    // Hitung harga otomatis berdasarkan HNA (menggunakan setting dinamis)
    public static function hitungHarga(float $hna, ?array $config = null): array
    {
        if (!$config) {
            $config = Setting::getPricingConfig();
        }

        $ppn = $config['ppn_persen'] / 100;
        $markupHv = $config['markup_hv_persen'] / 100;
        $markupResep = $config['markup_resep_persen'] / 100;

        $hargaJual = $hna * (1 + $ppn);
        $hargaHv = $hargaJual * (1 + $markupHv);
        $hargaResep = $hargaHv * (1 + $markupResep);

        return [
            'harga_jual' => round($hargaJual, 2),
            'harga_hv' => round($hargaHv, 2),
            'harga_resep' => round($hargaResep, 2),
        ];
    }

    // Scope untuk stok menipis
    public function scopeStokMenipis($query)
    {
        return $query->whereColumn('stok', '<=', 'stok_minimum');
    }

    // Scope berdasarkan grup
    public function scopeGrup($query, $grup)
    {
        return $query->where('grup', $grup);
    }

    // Label grup
    public function getGrupLabelAttribute(): string
    {
        return match($this->grup) {
            'hijau' => 'Obat Bebas',
            'merah' => 'Obat Keras / Narkotika',
            'biru' => 'Konsinyasi',
            default => '-',
        };
    }

    // Badge color
    public function getGrupBadgeAttribute(): string
    {
        return match($this->grup) {
            'hijau' => 'bg-green-500',
            'merah' => 'bg-red-500',
            'biru' => 'bg-blue-500',
            default => 'bg-gray-500',
        };
    }
}
