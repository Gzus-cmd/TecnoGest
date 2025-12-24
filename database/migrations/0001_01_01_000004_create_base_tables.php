<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tablas base del sistema:
 * - Proveedores
 * - Ubicaciones
 * - Sistemas Operativos
 */
return new class extends Migration
{
    public function up(): void
    {
        // ═══════════════════════════════════════════════════════════════
        // PROVEEDORES
        // ═══════════════════════════════════════════════════════════════
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 11);
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->string('address');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // ═══════════════════════════════════════════════════════════════
        // UBICACIONES
        // ═══════════════════════════════════════════════════════════════
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_workshop')->default(false);
            $table->string('pavilion');
            $table->smallInteger('apartment')->unsigned();
            $table->timestamps();

            // Índice de rendimiento
            $table->index('is_workshop', 'idx_locations_workshop');
        });

        // ═══════════════════════════════════════════════════════════════
        // SISTEMAS OPERATIVOS
        // ═══════════════════════════════════════════════════════════════
        Schema::create('o_s', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('version');
            $table->string('architecture');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('o_s');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('providers');
    }
};
