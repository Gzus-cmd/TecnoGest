<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla pivot para asignar componentes a dispositivos (computadoras/periféricos)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('componentables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_id')->constrained('components')->onDelete('cascade');
            $table->morphs('componentable'); // componentable_type + componentable_id
            $table->dateTime('assigned_at');
            $table->enum('status', ['Vigente', 'Removido', 'Desmantelado'])->default('Vigente');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('removed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Índices de rendimiento optimizados
            $table->index('component_id', 'idx_componentables_component');
            $table->index('status', 'idx_componentables_status');
            $table->index(['componentable_type', 'componentable_id', 'status'], 'idx_componentables_type_id_status');

            // Índices adicionales para historial de componentes
            $table->index(['component_id', 'status'], 'idx_component_status');
            $table->index(['componentable_type', 'componentable_id'], 'idx_device_lookup');
            $table->index('assigned_at', 'idx_assigned_at');
            $table->index(['component_id', 'assigned_at', 'status'], 'idx_history_main');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('componentables');
    }
};
