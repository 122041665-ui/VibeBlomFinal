<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('place_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type');
            $table->unsignedTinyInteger('rating')->default(0);
            $table->decimal('price', 10, 2);
            $table->string('city');
            $table->string('city_place_id')->nullable();
            $table->string('address')->nullable();
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->text('description')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('sent_to_flask')->default(false);
            $table->timestamp('sent_to_flask_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('place_submissions');
    }
};