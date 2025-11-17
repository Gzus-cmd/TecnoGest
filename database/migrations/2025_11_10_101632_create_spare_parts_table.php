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
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->text('description')->nullable();
            $table->string('part_number')->nullable();
            $table->enum('type', [
                'Placa Base',
                'Procesador',
                'Tarjeta Gráfica',
                'Memoria RAM',
                'Almacenamiento',
                'Monitor',
                'Teclado',
                'Mouse',
                'Adaptador de Red',
                'Fuente de Poder',
                'Gabinete',
                'Dispositivo de Audio',
                'Estabilizador',
                'Splitter',
                'Cabezal de Impresión',
                'Rodillo',
                'Fusor',
                'Lámpara de Proyector',
                'Lente',
                'Filtro de Aire',
                'Ventilador',
                'Otro'
            ]);
            $table->json('specifications')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_parts');
    }
};
