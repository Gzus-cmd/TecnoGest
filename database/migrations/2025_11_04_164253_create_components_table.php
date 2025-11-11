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
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->morphs('componentable');
            $table->string('serial')->unique();
            $table->date('input_date')->nullable();
            $table->date('output_date')->nullable();
            $table->enum('status', ['Operativo', 'Deficiente', 'Retirado']);
            $table->integer('warranty_months')->nullable();
            $table->foreignId('provider_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};
