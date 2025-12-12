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
        Schema::create('peripherals', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('location_id')->constrained('locations');
            $table->foreignId('computer_id')->nullable()->constrained('computers')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('computers', function (Blueprint $table) {
            $table->foreign('peripheral_id')->references('id')->on('peripherals')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peripherals');
    }
};
