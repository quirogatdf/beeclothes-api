<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Variant extends Model
{
    protected $fillable = [
        'product_id',
        'size_id',
        'color_id',
        'sku',
        'stock',
        'cost',
        'price',
        'promocional_price',
        'image_url',
    ];
    // --- Relations --- //
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    private function finalPrice(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->promocional_price && $this->promocional_price < $this->price) {
                    return (float) $this->promocional_price;
                }

            $category = $this->product->category ?? null;

            if($category && $category->global_discount > 0) {
                if (is_null($category->discount_expires_at) || $category->discount_expires_at>now()) {
                    $discountAmount = $this->price * ($category->global_discount / 100);
                    return (float) ($this->price - $discountAmount);
                }
            }

            return (float) $this->price;
            }
        );
    }
    protected $casts = [
        'stock' => 'integer',
        'cost' => 'float',
        'price' => 'float',
        'promocional_price' => 'float',
    ];
}
