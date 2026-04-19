<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key.
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set($key, $value)
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get the current system health status.
     */
    public static function getSystemStatus()
    {
        try {
            // Check Database Connectivity
            \Illuminate\Support\Facades\DB::connection()->getPdo();
        } catch (\Exception $e) {
            return [
                'label' => 'DB Connection Lost',
                'color' => 'red',
                'bg' => 'bg-red-100',
                'text' => 'text-red-700'
            ];
        }

        // Check Storage Writability (Critical for uploads)
        if (!is_writable(storage_path('app/public'))) {
            return [
                'label' => 'Storage Issue',
                'color' => 'red',
                'bg' => 'bg-red-100',
                'text' => 'text-red-700'
            ];
        }

        // Check Maintenance Mode
        if (self::get('maintenance_mode', '0') == '1') {
            return [
                'label' => 'Maintenance Mode',
                'color' => 'yellow',
                'bg' => 'bg-yellow-100',
                'text' => 'text-yellow-700'
            ];
        }

        // Default: Healthy
        return [
            'label' => 'Sync Active',
            'color' => 'green',
            'bg' => 'bg-green-100',
            'text' => 'text-green-700'
        ];
    }
}
