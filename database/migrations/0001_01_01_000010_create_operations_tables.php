<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tablas de operaciones:
 * - Traslados (transfers)
 * - Mantenimientos (maintenances)
 * 
 * Nota: maintenances se crea primero SIN la FK a transfers,
 * luego transfers con FK a maintenances,
 * y finalmente se agrega la FK de maintenances a transfers
 */
return new class extends Migration
{
    public function up(): void
    {
        // ═══════════════════════════════════════════════════════════════
        // MANTENIMIENTOS (sin FK a transfers inicialmente)
        // ═══════════════════════════════════════════════════════════════
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Preventivo', 'Correctivo']);
            $table->morphs('deviceable'); // deviceable_type + deviceable_id
            $table->foreignId('registered_by')->constrained('users');
            $table->enum('status', ['Pendiente', 'En Proceso', 'Finalizado']);
            $table->text('description')->nullable();
            $table->boolean('requires_workshop')->default(false);
            $table->foreignId('workshop_location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->string('device_previous_status')->nullable();
            $table->unsignedBigInteger('workshop_transfer_id')->nullable(); // Se agrega FK después
            $table->foreignId('previous_peripheral_id')->nullable()->constrained('peripherals')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Índices de rendimiento
            $table->index('status', 'idx_maintenances_status');
            $table->index('type', 'idx_maintenances_type');
            $table->index('registered_by', 'idx_maintenances_registered_by');
            $table->index(['status', 'type'], 'idx_maintenances_status_type');
        });

        // ═══════════════════════════════════════════════════════════════
        // TRASLADOS
        // ═══════════════════════════════════════════════════════════════
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->morphs('deviceable'); // deviceable_type + deviceable_id
            $table->foreignId('registered_by')->constrained('users');
            $table->foreignId('origin_id')->constrained('locations');
            $table->foreignId('destiny_id')->constrained('locations');
            $table->date('date');
            $table->text('reason')->nullable();
            $table->string('status')->default('Pendiente');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Índices de rendimiento
            $table->index('status', 'idx_transfers_status');
            $table->index('date', 'idx_transfers_date');
            $table->index('origin_id', 'idx_transfers_origin');
            $table->index('destiny_id', 'idx_transfers_destiny');
            $table->index('registered_by', 'idx_transfers_registered_by');
        });

        // Agregar FK de maintenances a transfers
        Schema::table('maintenances', function (Blueprint $table) {
            $table->foreign('workshop_transfer_id')
                ->references('id')
                ->on('transfers')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Eliminar FK antes de eliminar transfers
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropForeign(['workshop_transfer_id']);
        });
        
        Schema::dropIfExists('transfers');
        Schema::dropIfExists('maintenances');
    }
};
