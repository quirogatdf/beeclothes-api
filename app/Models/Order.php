<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'supplier_id',
        'external_id',
        'order_date',
        'tracking_code',
        'amount',
        'observations',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'order_date' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
