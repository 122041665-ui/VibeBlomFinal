<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mexican_states', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('code', 5)->unique();
            $table->timestamps();
        });

        $states = [
            ['AGS', 'Aguascalientes'], ['BC', 'Baja California'], ['BCS', 'Baja California Sur'],
            ['CAM', 'Campeche'], ['CHIS', 'Chiapas'], ['CHIH', 'Chihuahua'], ['CDMX', 'Ciudad de México'],
            ['COAH', 'Coahuila'], ['COL', 'Colima'], ['DGO', 'Durango'], ['GTO', 'Guanajuato'],
            ['GRO', 'Guerrero'], ['HGO', 'Hidalgo'], ['JAL', 'Jalisco'], ['MEX', 'Estado de México'],
            ['MICH', 'Michoacán'], ['MOR', 'Morelos'], ['NAY', 'Nayarit'], ['NL', 'Nuevo León'],
            ['OAX', 'Oaxaca'], ['PUE', 'Puebla'], ['QRO', 'Querétaro'], ['QROO', 'Quintana Roo'],
            ['SLP', 'San Luis Potosí'], ['SIN', 'Sinaloa'], ['SON', 'Sonora'], ['TAB', 'Tabasco'],
            ['TAMPS', 'Tamaulipas'], ['TLAX', 'Tlaxcala'], ['VER', 'Veracruz'], ['YUC', 'Yucatán'],
            ['ZAC', 'Zacatecas'],
        ];

        $now = now();
        DB::table('mexican_states')->insert(array_map(fn ($state) => [
            'code' => $state[0], 'name' => $state[1], 'created_at' => $now, 'updated_at' => $now,
        ], $states));
    }

    public function down(): void
    {
        Schema::dropIfExists('mexican_states');
    }
};
