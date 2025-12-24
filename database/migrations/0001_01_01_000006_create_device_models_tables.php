<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tablas de modelos de dispositivos:
 * - Modelos de Impresoras
 * - Modelos de Proyectores
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('printer_models', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->string('type');
            $table->boolean('color');
            $table->boolean('scanner');            
            $table->boolean('wifi');
            $table->boolean('ethernet');
            $table->timestamps();
        });

        Schema::create('projector_models', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->string('resolution');
            $table->unsignedBigInteger('lumens');
            $table->boolean('vga');
            $table->boolean('hdmi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projector_models');
        Schema::dropIfExists('printer_models');
    }
};
