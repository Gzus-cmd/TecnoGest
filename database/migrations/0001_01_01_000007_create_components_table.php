<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de componentes (instancias polimórficas de hardware)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->morphs('componentable'); // componentable_type + componentable_id
            $table->string('serial')->unique();
            $table->date('input_date')->nullable();
            $table->date('output_date')->nullable();
            $table->enum('status', ['Operativo', 'Deficiente', 'Retirado']);
            $table->integer('warranty_months')->nullable();
            $table->foreignId('provider_id')->constrained('providers');
            $table->foreignId('registered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('retired_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Índices de rendimiento
            $table->index('status', 'idx_components_status');
            $table->index('provider_id', 'idx_components_provider');
            $table->index('input_date', 'idx_components_input_date');
            $table->index(['status', 'provider_id'], 'idx_components_status_provider');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};
