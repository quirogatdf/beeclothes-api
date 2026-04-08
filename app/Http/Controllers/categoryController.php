<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class categoryController extends Controller
{
    // Get all categories (flat list)
    public function index()
    {
        return Category::all();
    }

    // Get categories in tree structure (for menu)
    public function tree()
    {
        $rootCategories = Category::root()
            ->with('children.children')
            ->get();

        return $rootCategories->map(function ($category) {
            return $this->formatCategoryForMenu($category);
        });
    }

    // Get products for a category (including all descendants)
    public function products($id)
    {
        $category = Category::findOrFail($id);
        
        // Get all category IDs (this category + all descendants)
        $categoryIds = $category->getAllDescendantIds();

        $products = Product::with([
            'category',
            'variants.size',
            'variants.color',
        ])
            ->where('is_active', 1)
            ->whereIn('category_id', $categoryIds)
            ->whereHas('variants', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->get()
            ->map(function ($product) {
                $product->variants = $product->variants->filter(fn ($v) => $v->stock > 0)->values();
                return $product;
            });

        return $products;
    }

    // Format category for menu response
    private function formatCategoryForMenu(Category $category): array
    {
        $item = [
            'id' => $category->id,
            'label' => $category->name,
            'path' => $category->getMenuPath(),
            'order' => $category->menu_order ?? 0,
            'is_visible' => $category->show_in_menu,
            'is_container' => $category->children->count() > 0,
        ];

        // Recursively add children
        if ($category->children->count() > 0) {
            $item['children'] = $category->children->map(function ($child) {
                return $this->formatCategoryForMenu($child);
            })->toArray();
        }

        return $item;
    }

    public function show($id)
    {
        return Category::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'global_discount' => 'nullable|integer|min:0|max:100',
            'discount_expires_at' => 'nullable|date',
            'parent_id' => 'nullable|exists:categories,id',
            'menu_order' => 'nullable|integer|min:0',
            'show_in_menu' => 'nullable|boolean',
        ]);

        // Generate slug if not provided
        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        }

        return Category::create($validated);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
            'global_discount' => 'nullable|integer|min:0|max:100',
            'discount_expires_at' => 'nullable|date',
            'parent_id' => 'nullable|exists:categories,id',
            'menu_order' => 'nullable|integer|min:0',
            'show_in_menu' => 'nullable|boolean',
        ]);

        $category->update($validated);
        return $category;
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(null, 204);
    }
}
