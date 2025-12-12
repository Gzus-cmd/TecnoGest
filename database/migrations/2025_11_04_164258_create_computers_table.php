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
        Schema::create('computers', function (Blueprint $table) {
            $table->id();
            $table->string('serial')->unique();
            $table->foreignId('location_id')->constrained();
            $table->enum('status', ['Activo', 'Inactivo', 'En Mantenimiento', 'Desmantelado']);
            $table->string('ip_address')->nullable();
            $table->foreignId('os_id')->constrained('o_s')->onDelete('cascade');
            $table->foreignId('peripheral_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('computers');
    }
};
