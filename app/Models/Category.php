<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'global_discount',
        'discount_expires_at',
        'parent_id',
        'menu_order',
        'show_in_menu',
    ];

    protected $casts = [
        'discount_expires_at' => 'datetime',
        'global_discount' => 'integer',
        'show_in_menu' => 'boolean',
    ];

    // Relación jerárquica
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('menu_order');
    }

    // Scope para obtener solo categorías raíz (padres)
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id')->orderBy('menu_order');
    }

    // Obtener todos los IDs de la categoría + sus subcategorías (recursivo)
    public function getAllDescendantIds(): array
    {
        $ids = [$this->id];

        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllDescendantIds());
        }

        return $ids;
    }

    // Obtener la ruta completa del menú (para el frontend)
    public function getMenuPath(): string
    {
        return '/products?category=' . $this->slug;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
