<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE sales MODIFY status ENUM('completed','cancelled','refunded','pending') DEFAULT 'completed'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE sales MODIFY status ENUM('completed','cancelled','refunded') DEFAULT 'completed'");
    }
};
