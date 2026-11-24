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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();
            $table->string('model');
            $table->enum('type', ['Sedan', 'SUV', 'MPV', 'Van', '4x4']);
            $table->unsignedInteger('capacity')->default(4);
            $table->unsignedInteger('current_odometer')->default(0);
            $table->enum('status', ['available', 'maintenance', 'inactive'])->default('available')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
