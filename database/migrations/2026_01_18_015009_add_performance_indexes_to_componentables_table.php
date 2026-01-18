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
        Schema::table('componentables', function (Blueprint $table) {
            // Índices compuestos para optimizar las consultas más frecuentes del ComponentHistoryResource
            // Estas queries se ejecutan constantemente en el historial de componentes

            // Índice para filtros por tipo de componente + estado de asignación
            $table->index(['component_id', 'status'], 'idx_component_status');

            // Índice para filtros por tipo de dispositivo + ID de dispositivo
            $table->index(['componentable_type', 'componentable_id'], 'idx_device_lookup');

            // Índice para ordenamiento por fecha de asignación (muy usado)
            $table->index('assigned_at', 'idx_assigned_at');

            // Índice compuesto para el query principal del historial
            $table->index(['component_id', 'assigned_at', 'status'], 'idx_history_main');
        });

        // Optimizar tabla components también
        Schema::table('components', function (Blueprint $table) {
            // Índice para filtros frecuentes por tipo + estado
            $table->index(['componentable_type', 'status'], 'idx_type_status');

            // Índice para búsquedas por serial (muy frecuente)
            if (!Schema::hasIndex('components', ['serial'])) {
                $table->index('serial', 'idx_serial');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('componentables', function (Blueprint $table) {
            $table->dropIndex('idx_component_status');
            $table->dropIndex('idx_device_lookup');
            $table->dropIndex('idx_assigned_at');
            $table->dropIndex('idx_history_main');
        });

        Schema::table('components', function (Blueprint $table) {
            $table->dropIndex('idx_type_status');
            $table->dropIndex('idx_serial');
        });
    }
};
