<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('place_submissions', 'rejection_reason')) {
            Schema::table('place_submissions', function (Blueprint $table) {
                $table->string('rejection_reason', 500)->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('place_submissions', 'rejection_reason')) {
            Schema::table('place_submissions', fn (Blueprint $table) => $table->dropColumn('rejection_reason'));
        }
    }
};
