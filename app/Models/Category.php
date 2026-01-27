<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'global_discount',
        'discount_expires_at',
    ];
    protected $casts = [
        'discount_expires_at' => 'datetime',
        'global_discount' => 'integer',
    ];
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
