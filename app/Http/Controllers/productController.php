<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class productController extends Controller
{
    // Get all products with their variants and category (only active with stock) - PUBLIC
    public function index()
    {
        $products = Product::with([
            'category',
            'variants.size',
            'variants.color',
        ])
            ->where('is_active', 1)
            ->whereHas('variants', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->get()
            ->map(function ($product) {
                // Filtrar variantes sin stock del resultado
                $product->variants = $product->variants->filter(fn ($v) => $v->stock > 0)->values();

                return $product;
            });

        return $products;
    }

    // Get ALL products for admin (no filtering) - ADMIN ONLY
    public function adminIndex()
    {
        $products = Product::with([
            'category',
            'variants.size',
            'variants.color',
        ])->get();

        return $products;
    }

    // Get by id with variants, size and color
    public function show($id)
    {
        $product = Product::with([
            'category',
            'variants.size',
            'variants.color',
        ])->findOrFail($id);

        return response()->json($product);
    }

    // Create a new product
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',

            // Datos de las Variantes (Array)
            'variants' => 'required|array|min:1',

            // Validaciones por cada item del array
            'variants.*.sku' => 'required|string|unique:variants,sku',
            'variants.*.size_id' => 'required|exists:sizes,id',
            'variants.*.color_id' => 'nullable|exists:colors,id',
            'variants.*.stock' => 'required|integer|min:0',

            // Validar Dinero (Nuevos campos)
            'variants.*.cost' => 'required|numeric|min:0',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.promocional_price' => 'nullable|numeric|lt:variants.*.price',

            // Validar URL de imagen
            'variants.*.image_url' => 'nullable|url|max:2048',
        ]);

        return DB::transaction(function () use ($validated) {
            // A. Crear Producto Padre
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $product->variants()->createMany($validated['variants']);

            // 3. Respuesta JSON
            return response()->json([
                'message' => 'Producto creado correctamente',
                'data' => $product->load('variants', 'category'),
            ], 201);
        });
    }

    // Update an existing product
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',

            // Datos de las Variantes (Array)
            'variants' => 'sometimes|array|min:1',

            // Validaciones por cada item del array
            'variants.*.sku' => 'required|string|unique:variants,sku,'.$id.',product_id',
            'variants.*.size_id' => 'required|exists:sizes,id',
            'variants.*.color_id' => 'nullable|exists:colors,id',
            'variants.*.stock' => 'required|integer|min:0',

            // Validar Dinero
            'variants.*.cost' => 'required|numeric|min:0',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.promocional_price' => 'nullable|numeric|lt:variants.*.price',

            // Validar URL de imagen
            'variants.*.image_url' => 'nullable|url|max:2048',
        ]);

        return DB::transaction(function () use ($validated, $product) {
            // Actualizar datos del producto
            $product->update([
                'name' => $validated['name'] ?? $product->name,
                'description' => $validated['description'] ?? $product->description,
                'category_id' => $validated['category_id'] ?? $product->category_id,
                'is_active' => $validated['is_active'] ?? $product->is_active,
            ]);

            // Si se proporcionan variantes, actualizarlas
            if (isset($validated['variants'])) {
                $product->variants()->delete();
                $product->variants()->createMany($validated['variants']);
            }

            return response()->json([
                'message' => 'Producto actualizado correctamente',
                'data' => $product->load('variants', 'category'),
            ]);
        });
    }
}
