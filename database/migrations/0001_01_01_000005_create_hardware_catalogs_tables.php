<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tablas de catálogos de componentes de hardware:
 * - CPUs, GPUs, RAMs, ROMs
 * - Motherboards, Power Supplies, Tower Cases
 * - Network Adapters
 * - Monitores, Teclados, Mouse
 * - Audio Devices, Stabilizers, Splitters
 */
return new class extends Migration
{
    public function up(): void
    {
        // ═══════════════════════════════════════════════════════════════
        // COMPONENTES INTERNOS DE COMPUTADORA
        // ═══════════════════════════════════════════════════════════════
        
        Schema::create('c_p_u_s', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->string('socket');
            $table->integer('cores');
            $table->integer('threads');
            $table->string('architecture');
            $table->integer('watts');
            $table->timestamps();
        });

        Schema::create('g_p_u_s', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->string('memory');
            $table->integer('capacity');
            $table->string('interface');
            $table->float('frequency');
            $table->integer('watts');
            $table->timestamps();
        });

        Schema::create('r_a_m_s', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->string('type');
            $table->string('technology');
            $table->integer('capacity');
            $table->float('frequency');
            $table->integer('watts');
            $table->timestamps();
        });

        Schema::create('r_o_m_s', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->string('type');
            $table->integer('capacity');
            $table->string('interface');
            $table->float('speed');
            $table->integer('watts');
            $table->timestamps();
        });

        Schema::create('motherboards', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->string('socket');
            $table->string('chipset');
            $table->string('format');
            $table->integer('slots_ram');
            $table->integer('ports_sata');
            $table->integer('ports_m2')->nullable();
            $table->integer('watts');
            $table->timestamps();
        });

        Schema::create('power_supplies', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->string('certification')->nullable();
            $table->integer('watts');
            $table->timestamps();
        });

        Schema::create('tower_cases', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->string('format');
            $table->timestamps();
        });

        Schema::create('network_adapters', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->string('type');
            $table->float('speed');
            $table->string('interface');
            $table->integer('watts');
            $table->timestamps();
        });

        // ═══════════════════════════════════════════════════════════════
        // PERIFÉRICOS
        // ═══════════════════════════════════════════════════════════════

        Schema::create('monitors', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->float('size');
            $table->string('resolution');
            $table->boolean('vga');
            $table->boolean('hdmi');
            $table->timestamps();
        });

        Schema::create('keyboards', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->string('connection');
            $table->string('language');
            $table->timestamps();
        });

        Schema::create('mice', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->string('connection');
            $table->timestamps();
        });

        Schema::create('audio_devices', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->string('type');
            $table->integer('speakers');
            $table->timestamps();
        });

        Schema::create('stabilizers', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->integer('outlets');
            $table->integer('watts');
            $table->timestamps();
        });

        Schema::create('splitters', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->integer('ports');
            $table->string('frequency');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('splitters');
        Schema::dropIfExists('stabilizers');
        Schema::dropIfExists('audio_devices');
        Schema::dropIfExists('mice');
        Schema::dropIfExists('keyboards');
        Schema::dropIfExists('monitors');
        Schema::dropIfExists('network_adapters');
        Schema::dropIfExists('tower_cases');
        Schema::dropIfExists('power_supplies');
        Schema::dropIfExists('motherboards');
        Schema::dropIfExists('r_o_m_s');
        Schema::dropIfExists('r_a_m_s');
        Schema::dropIfExists('g_p_u_s');
        Schema::dropIfExists('c_p_u_s');
    }
};
