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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Preventivo', 'Correctivo']);
            $table->morphs('deviceable');
            $table->foreignId('registered_by')->constrained('users');
            $table->enum('status', ['Pendiente', 'En Proceso', 'Finalizado']);
            $table->text('description')->nullable();
            $table->boolean('requires_workshop')->default(false);
            $table->foreignId('workshop_location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->string('device_previous_status')->nullable();
            $table->unsignedBigInteger('workshop_transfer_id')->nullable();
            $table->foreignId('previous_peripheral_id')->nullable()->constrained('peripherals')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
