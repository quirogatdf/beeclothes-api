<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Size;
use App\Models\Color;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Bikinis',
            'Indumentaria Deportiva',
            'Remeras',
            'Shorts',
            'Buzos',
            'Pijamas Adultos', // Corregido "Pijamas"
            'Pijamas Niños',   // Corregido "Pijamas"
            'Medias Adultos',
            'Medias Infantiles',
            'Accesorios'
        ];
        foreach ($categories as $catName) {
            Category::firstOrCreate(
                ['name' => $catName],
                [
                    'slug' => Str::slug($catName),
                    'global_discount' => 0
                ]
            );
        }
        $sizes = [
            //Talles infantiles
            '4', '6', '8', '10', '12', '14', '16',
            // Talles Adultos
            'Talle 1', 'Talle 2', 'Talle 3', 'Talle 4', 'Talle 5', 'Talle 6',
            'XS', 'S', 'M', 'L', 'XL', 'XXL',
            'Único'
        ];
        foreach ($sizes as $sizeName) {
            Size::firstOrCreate(
                ['name' => $sizeName],
                ['slug' => Str::slug($sizeName)]
            );
        }

        $colors = [
            ['name' => 'Negro', 'hex' => '#000000'],
            ['name' => 'Blanco', 'hex' => '#FFFFFF'],
            ['name' => 'Gris Melange', 'hex' => '#808080'],
            ['name' => 'Azul Marino', 'hex' => '#000080'],
            ['name' => 'Rojo', 'hex' => '#FF0000'],
            ['name' => 'Rosa', 'hex' => '#FFC0CB'],
            ['name' => 'Verde Militar', 'hex' => '#4B5320'],
            ['name' => 'Estampado', 'hex' => null], // Para bikinis o remeras con diseño
            ['name' => 'Multicolor', 'hex' => null],
        ];

        foreach ($colors as $color) {
            Color::firstOrCreate(
                ['name' => $color['name']],
                ['hex_code' => $color['hex']]
            );
        }
    }
}
