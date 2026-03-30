<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();

            // Usuario que creó la publicación
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Información enviada desde el formulario
            $table->string('name');          // Nombre del lugar
            $table->string('city');          // Ciudad
            $table->decimal('price', 10, 2); // Precio aproximado

            $table->string('photo')->nullable(); // Archivo de imagen guardada

            // Ubicación con Mapbox
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            // Descripción del lugar
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};