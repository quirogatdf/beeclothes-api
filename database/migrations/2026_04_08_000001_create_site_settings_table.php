<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        // Insertar configuración inicial
        DB::table('site_settings')->insert([
            'key' => 'site_config',
            'value' => json_encode([
                'store_name' => 'BeeClothes',
                'store_description' => 'Tu tienda de ropa online',
                'contact_phone' => '',
                'contact_email' => '',
                'social_links' => [
                    'instagram' => '',
                    'facebook' => '',
                    'whatsapp' => '',
                ],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insertar menú personalizado inicial
        DB::table('site_settings')->insert([
            'key' => 'custom_menu_items',
            'value' => json_encode([
                ['label' => 'Inicio', 'path' => '/', 'order' => 0, 'is_visible' => true],
                ['label' => 'Preguntas Frecuentes', 'path' => '/preguntas-frecuentes', 'order' => 100, 'is_visible' => true],
                ['label' => 'Contacto', 'path' => '/contacto', 'order' => 101, 'is_visible' => true],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};