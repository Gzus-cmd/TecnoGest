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
            $table->enum('status', ['Pendiente', 'En Progreso', 'Finalizado']);
            $table->text('description');
            $table->boolean('requires_workshop')->default(false);
            $table->string('device_previous_status')->nullable();
            $table->unsignedBigInteger('workshop_transfer_id')->nullable();
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
