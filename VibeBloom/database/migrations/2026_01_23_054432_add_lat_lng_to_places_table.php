<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('places', function (Blueprint $table) {

            // ✅ Solo agrega si NO existe
            if (!Schema::hasColumn('places', 'lat')) {
                $table->decimal('lat', 10, 7)->nullable()->after('city');
            }

            if (!Schema::hasColumn('places', 'lng')) {
                $table->decimal('lng', 10, 7)->nullable()->after('lat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            if (Schema::hasColumn('places', 'lng')) $table->dropColumn('lng');
            if (Schema::hasColumn('places', 'lat')) $table->dropColumn('lat');
        });
    }
};