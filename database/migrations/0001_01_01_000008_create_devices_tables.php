<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de dispositivos principales:
 * - Computadoras
 * - Periféricos (conjuntos)
 * - Impresoras
 * - Proyectores
 * 
 * Nota: Se crea computers primero SIN la FK a peripherals,
 * luego peripherals con FK a computers,
 * y finalmente se agrega la FK de computers a peripherals
 */
return new class extends Migration
{
    public function up(): void
    {
        // ═══════════════════════════════════════════════════════════════
        // COMPUTADORAS (sin FK a peripherals inicialmente)
        // ═══════════════════════════════════════════════════════════════
        Schema::create('computers', function (Blueprint $table) {
            $table->id();
            $table->string('serial')->unique();
            $table->foreignId('location_id')->constrained('locations');
            $table->enum('status', ['Activo', 'Inactivo', 'En Mantenimiento', 'Desmantelado']);
            $table->string('ip_address')->nullable();
            $table->foreignId('os_id')->constrained('o_s')->onDelete('cascade');
            $table->unsignedBigInteger('peripheral_id')->nullable(); // Se agrega FK después
            $table->timestamps();

            // Índices de rendimiento
            $table->index('location_id', 'idx_computers_location');
            $table->index('status', 'idx_computers_status');
            $table->index('os_id', 'idx_computers_os');
            $table->index(['status', 'location_id'], 'idx_computers_status_location');
        });

        // ═══════════════════════════════════════════════════════════════
        // PERIFÉRICOS (conjuntos de monitor, teclado, mouse, etc.)
        // ═══════════════════════════════════════════════════════════════
        Schema::create('peripherals', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('location_id')->constrained('locations');
            $table->foreignId('computer_id')->nullable()->constrained('computers')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Índices de rendimiento
            $table->index('location_id', 'idx_peripherals_location');
            $table->index('computer_id', 'idx_peripherals_computer');
        });

        // Agregar FK de computers a peripherals (relación bidireccional)
        Schema::table('computers', function (Blueprint $table) {
            $table->foreign('peripheral_id')
                ->references('id')
                ->on('peripherals')
                ->onDelete('set null');
        });

        // ═══════════════════════════════════════════════════════════════
        // IMPRESORAS
        // ═══════════════════════════════════════════════════════════════
        Schema::create('printers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modelo_id')->constrained('printer_models')->onDelete('cascade');
            $table->string('serial')->unique();
            $table->foreignId('location_id')->constrained('locations');
            $table->string('ip_address')->nullable();
            $table->enum('status', ['Activo', 'Inactivo', 'En Mantenimiento', 'Desmantelado'])->default('Activo');
            $table->integer('warranty_months')->nullable();
            $table->date('input_date')->nullable();
            $table->date('output_date')->nullable();
            $table->timestamps();

            // Índices de rendimiento
            $table->index('location_id', 'idx_printers_location');
            $table->index('status', 'idx_printers_status');
            $table->index(['status', 'location_id'], 'idx_printers_status_location');
        });

        // ═══════════════════════════════════════════════════════════════
        // PROYECTORES
        // ═══════════════════════════════════════════════════════════════
        Schema::create('projectors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modelo_id')->constrained('projector_models')->onDelete('cascade');
            $table->string('serial')->unique();
            $table->foreignId('location_id')->constrained('locations');
            $table->enum('status', ['Activo', 'Inactivo', 'En Mantenimiento', 'Desmantelado'])->default('Activo');
            $table->integer('warranty_months')->nullable();
            $table->date('input_date')->nullable();
            $table->date('output_date')->nullable();
            $table->timestamps();

            // Índices de rendimiento
            $table->index('location_id', 'idx_projectors_location');
            $table->index('status', 'idx_projectors_status');
            $table->index(['status', 'location_id'], 'idx_projectors_status_location');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projectors');
        Schema::dropIfExists('printers');
        
        // Eliminar FK antes de eliminar peripherals
        Schema::table('computers', function (Blueprint $table) {
            $table->dropForeign(['peripheral_id']);
        });
        
        Schema::dropIfExists('peripherals');
        Schema::dropIfExists('computers');
    }
};
