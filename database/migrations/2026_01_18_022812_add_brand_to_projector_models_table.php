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
        Schema::table('projector_models', function (Blueprint $table) {
            // Agregar columna brand después de id
            $table->string('brand')->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projector_models', function (Blueprint $table) {
            $table->dropColumn('brand');
        });
    }
};
