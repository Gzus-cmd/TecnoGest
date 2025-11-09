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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printers');
    }
};
