<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('places', function (Blueprint $table) {

            // fotos extra (json)
            if (!Schema::hasColumn('places', 'photos')) {
                $table->json('photos')->nullable()->after('photo');
            }

            // tipo de lugar
            if (!Schema::hasColumn('places', 'type')) {
                $table->string('type', 80)->nullable()->after('city');
            }

            // rating 1-5
            if (!Schema::hasColumn('places', 'rating')) {
                $table->unsignedTinyInteger('rating')->default(0)->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            if (Schema::hasColumn('places', 'photos')) {
                $table->dropColumn('photos');
            }
            if (Schema::hasColumn('places', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('places', 'rating')) {
                $table->dropColumn('rating');
            }
        });
    }
};
