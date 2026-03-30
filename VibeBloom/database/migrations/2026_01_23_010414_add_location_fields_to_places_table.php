<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Checar existencia ANTES de alterar la tabla
        $hasAddress   = Schema::hasColumn('places', 'address');
        $hasReference = Schema::hasColumn('places', 'reference');
        $hasLat       = Schema::hasColumn('places', 'lat');
        $hasLng       = Schema::hasColumn('places', 'lng');

        Schema::table('places', function (Blueprint $table) use ($hasAddress, $hasReference, $hasLat, $hasLng) {

            if (!$hasAddress) {
                $table->string('address')->nullable()->after('city');
            }

            if (!$hasReference) {
                // Si address no existe, after('address') puede fallar.
                // Entonces, lo ponemos después de city si address ya existía.
                $afterCol = Schema::hasColumn('places', 'address') ? 'address' : 'city';
                $table->string('reference', 500)->nullable()->after($afterCol);
            }

            if (!$hasLat) {
                $table->decimal('lat', 10, 7)->nullable()->after('reference');
            }

            if (!$hasLng) {
                $table->decimal('lng', 10, 7)->nullable()->after('lat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            // ✅ En down, también conviene checar antes de drop
            if (Schema::hasColumn('places', 'lng')) $table->dropColumn('lng');
            if (Schema::hasColumn('places', 'lat')) $table->dropColumn('lat');
            if (Schema::hasColumn('places', 'reference')) $table->dropColumn('reference');
            // address puede existir de antes, normalmente NO lo tiraría si ya estaba.
            // Si tú sabes que esta migración lo creó, descomenta:
            // if (Schema::hasColumn('places', 'address')) $table->dropColumn('address');
        });
    }
};
