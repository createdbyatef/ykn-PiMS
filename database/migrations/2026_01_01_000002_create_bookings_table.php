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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('request_type', ['vehicle_and_pilot', 'vehicle_only', 'pilot_only'])->index();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('pilot_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('destination');
            $table->text('purpose');
            $table->string('driver_license_no')->nullable();
            $table->boolean('is_urgent')->default(false)->index();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending')->index();
            $table->enum('rejection_reason', [
                'all_vehicles_in_use',
                'vehicle_already_booked',
                'vehicle_under_maintenance',
                'other',
            ])->nullable();
            $table->string('rejection_remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->unsignedInteger('odometer_start')->nullable();
            $table->unsignedInteger('odometer_end')->nullable();
            $table->text('pilot_remarks')->nullable();
            $table->timestamps();

            $table->index(['status', 'start_datetime', 'end_datetime'], 'bookings_status_window_idx');
            $table->index(['vehicle_id', 'status', 'start_datetime', 'end_datetime'], 'bookings_vehicle_conflict_idx');
            $table->index(['pilot_id', 'status', 'start_datetime', 'end_datetime'], 'bookings_pilot_conflict_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
