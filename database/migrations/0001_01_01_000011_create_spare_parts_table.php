<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de catálogo de repuestos
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->text('description')->nullable();
            $table->string('part_number')->nullable();
            // Solo tipos para repuestos de impresoras y proyectores
            $table->enum('type', [
                // Repuestos de Impresoras
                'Cabezal de Impresión',
                'Cartucho de Tinta',
                'Tóner',
                'Rodillo',
                'Fusor',
                'Bandeja de Papel',
                'Correa de Transferencia',
                'Unidad de Imagen',
                // Repuestos de Proyectores
                'Lámpara de Proyector',
                'Lente',
                'Filtro de Aire',
                'Ventilador',
                'Panel LCD',
                'Rueda de Color',
                'Placa de Control',
                // General
                'Otro'
            ]);
            $table->json('specifications')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spare_parts');
    }
};
