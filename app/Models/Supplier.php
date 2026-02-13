<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'cuit',
        'phone',
        'mail',
        'link',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
