<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('place_submissions', function (Blueprint $table) {
            $table->unsignedBigInteger('platform_submission_id')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('place_submissions', function (Blueprint $table) {
            $table->dropUnique(['platform_submission_id']);
            $table->dropColumn('platform_submission_id');
        });
    }
};
