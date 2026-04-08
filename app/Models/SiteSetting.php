<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    // Obtener un setting por clave
    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    // Guardar un setting por clave
    public static function setValue(string $key, $value): SiteSetting
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}