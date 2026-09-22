<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
    ];

    public const CACHE_KEY = 'app_settings_cache';

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget(self::CACHE_KEY);
        });

        static::deleted(function () {
            Cache::forget(self::CACHE_KEY);
        });
    }

    /**
     * Get all settings as key => value associative array with caching.
     */
    public static function getAllSettings(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            try {
                return self::pluck('value', 'key')->toArray();
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    /**
     * Get setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = self::getAllSettings();

        if (array_key_exists($key, $settings) && $settings[$key] !== null && $settings[$key] !== '') {
            return $settings[$key];
        }

        return $default;
    }

    /**
     * Set/update a setting value.
     */
    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'text'): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type'  => $type,
            ]
        );

        Cache::forget(self::CACHE_KEY);

        return $setting;
    }
}
