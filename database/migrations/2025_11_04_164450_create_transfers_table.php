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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->morphs('deviceable');
            $table->foreignId('registered_by')->constrained('users');
            $table->foreignId('origin_id')->constrained('locations');
            $table->foreignId('destiny_id')->constrained('locations');
            $table->date('date');
            $table->text('reason')->nullable();
            $table->string('status')->default('Pendiente');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Agregar foreign key de maintenances a transfers ahora que transfers existe
        Schema::table('maintenances', function (Blueprint $table) {
            $table->foreign('workshop_transfer_id')
                ->references('id')
                ->on('transfers')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar foreign key antes de eliminar la tabla
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropForeign(['workshop_transfer_id']);
        });
        
        Schema::dropIfExists('transfers');
    }
};
