<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('points_of_interest', function (Blueprint $table) {
            $table->foreignId('poi_type_id')->nullable()->after('type')->constrained('poi_types')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('points_of_interest', function (Blueprint $table) {
            $table->dropConstrainedForeignId('poi_type_id');
        });
    }
};
