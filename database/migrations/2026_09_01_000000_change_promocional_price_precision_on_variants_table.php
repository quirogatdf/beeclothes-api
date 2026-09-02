<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * La columna promocional_price era decimal(5,2) = tope 999.99,
     * insuficiente para precios en ARS. Se alinea con price/cost (10,2).
     */
    public function up(): void
    {
        Schema::table('variants', function (Blueprint $table) {
            $table->decimal('promocional_price', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variants', function (Blueprint $table) {
            $table->decimal('promocional_price', 5, 2)->nullable()->change();
        });
    }
};