<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::table('places', function (Blueprint $table) {
    if (!Schema::hasColumn('places', 'address')) {
        $table->string('address')->nullable()->after('city');
    }
    if (!Schema::hasColumn('places', 'reference')) {
        $table->string('reference', 500)->nullable()->after('address');
    }
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            //
        });
    }
};
