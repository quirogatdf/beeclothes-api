<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class productController extends Controller
{
    //Get all products with their variants and category
    public function index()
    {
        return Product::with([
            'category',
            'variants.size',
            'variants.color',
        ])->get();
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
            'variants.*.promotional_price' => 'nullable|numeric|lt:variants.*.price',
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
                'data' => $product->load('variants', 'category')
            ], 201);
        });
    }
}
