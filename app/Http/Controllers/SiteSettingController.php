<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\Category;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    // GET /admin/config - Obtener configuración del sitio
    public function getConfig()
    {
        $config = SiteSetting::getValue('site_config', [
            'store_name' => 'BeeClothes',
            'store_description' => '',
            'contact_phone' => '',
            'contact_email' => '',
            'social_links' => [
                'instagram' => '',
                'facebook' => '',
                'whatsapp' => '',
            ],
        ]);

        return response()->json($config);
    }

    // PUT /admin/config - Guardar configuración del sitio
    public function updateConfig(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'nullable|string|max:255',
            'store_description' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email',
            'social_links' => 'nullable|array',
            'social_links.instagram' => 'nullable|string',
            'social_links.facebook' => 'nullable|string',
            'social_links.whatsapp' => 'nullable|string',
        ]);

        SiteSetting::setValue('site_config', $validated);

        return response()->json([
            'message' => 'Configuración guardada correctamente',
            'data' => $validated,
        ]);
    }

    // GET /menu - Obtener menú (solo custom items, NO categorías automáticas)
    public function getMenu()
    {
        // Solo retornamos los custom items configurados en el backoffice
        // Las categorías ya no se agregan automáticamente
        // El usuario las configura manualmente desde el panel de menús
        $customItems = SiteSetting::getValue('custom_menu_items', []);

        // Filtrar solo los items visibles
        $visibleItems = array_filter($customItems, function ($item) {
            return $item['is_visible'] ?? true;
        });

        // Ordenar por 'order'
        usort($visibleItems, function ($a, $b) {
            return ($a['order'] ?? 0) - ($b['order'] ?? 0);
        });

        return response()->json(array_values($visibleItems));
    }

    // PUT /admin/menu - Guardar menú (solo custom items)
    public function saveMenu(Request $request)
    {
        $validated = $request->validate([
            'menu' => 'required|array',
            'menu.*.label' => 'required|string|max:255',
            'menu.*.path' => 'nullable|string|max:255',
            'menu.*.order' => 'nullable|integer',
            'menu.*.is_visible' => 'nullable|boolean',
        ]);

        // Guardar solo los items que NO son categorías (son los que vienen del frontend)
        // El frontend envía los items custom del menú (los de "Inicio", "Preguntas Frecuentes", etc)
        // Las categorías se gestionan desde su propio CRUD

        // Los items con category_id son categorías, los otros son custom
        $customItems = array_filter($validated['menu'], function ($item) {
            return !isset($item['category_id']);
        });

        SiteSetting::setValue('custom_menu_items', array_values($customItems));

        return response()->json([
            'message' => 'Menú guardado correctamente',
        ]);
    }

    // GET /admin/menu - Obtener menú para el backoffice (custom items + categorías disponibles)
    public function getMenuForAdmin()
    {
        // 1. Obtener custom items desde site_settings
        $customItems = SiteSetting::getValue('custom_menu_items', []);

        // 2. Obtener todas las categorías disponibles para agregar al menú
        $categoryItems = Category::where('show_in_menu', true)
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => 'cat_' . $category->id,
                    'label' => $category->name,
                    'path' => '/products?category_id=' . $category->id,
                    'order' => 0,
                    'is_visible' => true,
                    'category_id' => $category->id,
                ];
            })
            ->toArray();

        return response()->json([
            'custom_items' => $customItems,
            'available_categories' => $categoryItems,
        ]);
    }

    // Formatear categoría para el menú
    private function formatCategoryForMenu(Category $category): array
    {
        $item = [
            'id' => 'cat_' . $category->id,
            'label' => $category->name,
            'path' => '/products?category_id=' . $category->id,
            'order' => 100 + ($category->menu_order ?? $category->id),
            'is_visible' => $category->show_in_menu,
            'category_id' => $category->id,
            'is_container' => $category->children->count() > 0,
        ];

        if ($category->children->count() > 0) {
            $item['children'] = $category->children
                ->where('show_in_menu', true)
                ->map(function ($child) {
                    return $this->formatCategoryForMenu($child);
                })
                ->toArray();
        }

        return $item;
    }
}