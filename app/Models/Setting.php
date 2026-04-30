<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'label', 'group'];

    /**
     * Get setting value by key with default fallback
     */
    public static function getValue(string $key, $default = null): string
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : ($default ?? '');
        });
    }

    /**
     * Set a setting value
     */
    public static function setValue(string $key, string $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }

    /**
     * Get pricing percentages
     */
    public static function getPricingConfig(): array
    {
        return [
            'ppn_persen' => (float) self::getValue('ppn_persen', '10'),
            'markup_hv_persen' => (float) self::getValue('markup_hv_persen', '10'),
            'markup_resep_persen' => (float) self::getValue('markup_resep_persen', '8'),
        ];
    }
}
