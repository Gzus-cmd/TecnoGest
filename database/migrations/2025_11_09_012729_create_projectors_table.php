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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projectors');
    }
};
