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
        Schema::create('componentables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_id')->constrained()->onDelete('cascade');
            $table->morphs('componentable');
            $table->dateTime('assigned_at');
            $table->enum('status', ['Vigente', 'Removido', 'Desmantelado'])->default('Vigente');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('removed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('componentables');
    }
};
