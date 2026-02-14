<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('neighborhoods', function (Blueprint $table) {
            $table->json('boundary_coordinates')->nullable()->change();
            $table->decimal('center_lat', 10, 7)->nullable()->change();
            $table->decimal('center_lng', 10, 7)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('neighborhoods', function (Blueprint $table) {
            $table->json('boundary_coordinates')->nullable(false)->change();
            $table->decimal('center_lat', 10, 7)->nullable(false)->change();
            $table->decimal('center_lng', 10, 7)->nullable(false)->change();
        });
    }
};
