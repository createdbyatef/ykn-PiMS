<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingAvailabilityService
{
    /**
     * Determine whether a booking falls within YKN's 3-day notice window.
     */
    public function isUrgentSubmission(CarbonInterface $startDatetime, ?CarbonInterface $submittedAt = null): bool
    {
        $reference = ($submittedAt ?? now())->copy();

        return $startDatetime->lt($reference->addDays(3));
    }

    /**
     * Check if a vehicle has any overlapping APPROVED bookings in the given window.
     * Overlap condition: start_datetime < existing.end_datetime AND end_datetime > existing.start_datetime
     */
    public function hasVehicleConflict(
        int $vehicleId,
        CarbonInterface $start,
        CarbonInterface $end,
        ?int $ignoreBookingId = null
    ): bool {
        return Booking::query()
            ->where('status', Booking::STATUS_APPROVED)
            ->where('vehicle_id', $vehicleId)
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->where('start_datetime', '<', $end)
            ->where('end_datetime', '>', $start)
            ->exists();
    }

    /**
     * Find the conflicting booking for a vehicle, if any.
     */
    public function findVehicleConflict(
        int $vehicleId,
        CarbonInterface $start,
        CarbonInterface $end,
        ?int $ignoreBookingId = null
    ): ?Booking {
        return Booking::query()
            ->with('applicant')
            ->where('status', Booking::STATUS_APPROVED)
            ->where('vehicle_id', $vehicleId)
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->where('start_datetime', '<', $end)
            ->where('end_datetime', '>', $start)
            ->first();
    }

    /**
     * Check if a pilot has any overlapping APPROVED bookings in the given window.
     */
    public function hasPilotConflict(
        int $pilotId,
        CarbonInterface $start,
        CarbonInterface $end,
        ?int $ignoreBookingId = null
    ): bool {
        return Booking::query()
            ->where('status', Booking::STATUS_APPROVED)
            ->where('pilot_id', $pilotId)
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->where('start_datetime', '<', $end)
            ->where('end_datetime', '>', $start)
            ->exists();
    }

    /**
     * Find the conflicting booking for a pilot, if any.
     */
    public function findPilotConflict(
        int $pilotId,
        CarbonInterface $start,
        CarbonInterface $end,
        ?int $ignoreBookingId = null
    ): ?Booking {
        return Booking::query()
            ->with('applicant')
            ->where('status', Booking::STATUS_APPROVED)
            ->where('pilot_id', $pilotId)
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->where('start_datetime', '<', $end)
            ->where('end_datetime', '>', $start)
            ->first();
    }

    /**
     * Return all vehicles with availability metadata for a given time window.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function getVehiclesWithAvailability(
        CarbonInterface $start,
        CarbonInterface $end,
        ?int $ignoreBookingId = null
    ): \Illuminate\Support\Collection {
        return Vehicle::query()
            ->orderBy('plate_number')
            ->get()
            ->map(function (Vehicle $vehicle) use ($start, $end, $ignoreBookingId) {
                $conflict = $this->findVehicleConflict($vehicle->id, $start, $end, $ignoreBookingId);
                $available = $vehicle->isAvailable() && $conflict === null;

                $reason = null;
                if ($vehicle->status === Vehicle::STATUS_MAINTENANCE) {
                    $reason = 'Sedang diselenggara (Maintenance)';
                } elseif ($vehicle->status === Vehicle::STATUS_INACTIVE) {
                    $reason = 'Tidak aktif';
                } elseif ($conflict) {
                    $reason = "Telah ditempah ({$conflict->booking_code}: {$conflict->start_datetime->format('d/m H:i')} - {$conflict->end_datetime->format('d/m H:i')})";
                }

                return [
                    'vehicle' => $vehicle,
                    'available' => $available,
                    'reason' => $reason,
                    'conflict' => $conflict,
                ];
            });
    }

    /**
     * Return all active HQ pilots with availability metadata for a given time window.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function getPilotsWithAvailability(
        CarbonInterface $start,
        CarbonInterface $end,
        ?int $ignoreBookingId = null
    ): \Illuminate\Support\Collection {
        return User::query()
            ->pilots()
            ->orderBy('name')
            ->get()
            ->map(function (User $pilot) use ($start, $end, $ignoreBookingId) {
                $conflict = $this->findPilotConflict($pilot->id, $start, $end, $ignoreBookingId);
                $available = $pilot->is_active && $conflict === null;

                $reason = null;
                if (! $pilot->is_active) {
                    $reason = 'Pilot tidak aktif';
                } elseif ($conflict) {
                    $reason = "Ditugaskan ({$conflict->booking_code}: {$conflict->start_datetime->format('d/m H:i')} - {$conflict->end_datetime->format('d/m H:i')})";
                }

                return [
                    'pilot' => $pilot,
                    'available' => $available,
                    'reason' => $reason,
                    'conflict' => $conflict,
                ];
            });
    }

    /**
     * Get IDs of vehicles currently busy at a specific moment (default: now).
     *
     * @return Collection<int, Vehicle>
     */
    public function getAvailableVehiclesNow(): Collection
    {
        $now = now();
        $busyVehicleIds = Booking::query()
            ->where('status', Booking::STATUS_APPROVED)
            ->whereNotNull('vehicle_id')
            ->where('start_datetime', '<=', $now)
            ->where('end_datetime', '>=', $now)
            ->pluck('vehicle_id');

        return Vehicle::query()
            ->available()
            ->whereNotIn('id', $busyVehicleIds)
            ->orderBy('plate_number')
            ->get();
    }

    /**
     * Get HQ Pilots currently available (not on an active approved trip right now).
     *
     * @return Collection<int, User>
     */
    public function getAvailablePilotsNow(): Collection
    {
        $now = now();
        $busyPilotIds = Booking::query()
            ->where('status', Booking::STATUS_APPROVED)
            ->whereNotNull('pilot_id')
            ->where('start_datetime', '<=', $now)
            ->where('end_datetime', '>=', $now)
            ->pluck('pilot_id');

        return User::query()
            ->pilots()
            ->active()
            ->whereNotIn('id', $busyPilotIds)
            ->orderBy('name')
            ->get();
    }

    /**
     * Approve a booking with strict conflict & maintenance verification inside a DB transaction.
     *
     * @throws ValidationException
     */
    public function approveBooking(Booking $booking, User $approver, ?int $vehicleId, ?int $pilotId): Booking
    {
        return DB::transaction(function () use ($booking, $approver, $vehicleId, $pilotId) {
            /** @var Booking $lockedBooking */
            $lockedBooking = Booking::query()->lockForUpdate()->findOrFail($booking->id);

            if (! $lockedBooking->isPending()) {
                throw ValidationException::withMessages([
                    'status' => 'Hanya permohonan berstatus Menunggu Kelulusan (Pending) boleh diluluskan.',
                ]);
            }

            $errors = [];

            if ($lockedBooking->requiresVehicle()) {
                if (! $vehicleId) {
                    $errors['vehicle_id'] = 'Sila pilih kenderaan untuk permohonan ini.';
                } else {
                    $vehicle = Vehicle::query()->lockForUpdate()->find($vehicleId);
                    if (! $vehicle) {
                        $errors['vehicle_id'] = 'Kenderaan tidak dijumpai.';
                    } elseif ($vehicle->status === Vehicle::STATUS_MAINTENANCE) {
                        $errors['vehicle_id'] = "Kenderaan {$vehicle->plate_number} sedang diselenggara (Maintenance) dan tidak boleh ditugaskan.";
                    } elseif ($vehicle->status !== Vehicle::STATUS_AVAILABLE) {
                        $errors['vehicle_id'] = "Kenderaan {$vehicle->plate_number} tidak aktif dan tidak boleh ditugaskan.";
                    } elseif ($conflict = $this->findVehicleConflict($vehicle->id, $lockedBooking->start_datetime, $lockedBooking->end_datetime, $lockedBooking->id)) {
                        $errors['vehicle_id'] = "Pertindihan tempahan! Kenderaan {$vehicle->plate_number} telah ditempah oleh {$conflict->applicant->name} ({$conflict->booking_code}) dari {$conflict->start_datetime->format('d/m/Y H:i')} hingga {$conflict->end_datetime->format('d/m/Y H:i')}.";
                    }
                }
            } else {
                $vehicleId = null;
            }

            if ($lockedBooking->requiresPilot()) {
                if (! $pilotId) {
                    $errors['pilot_id'] = 'Sila pilih Pilot HQ untuk permohonan ini.';
                } else {
                    $pilot = User::query()->lockForUpdate()->find($pilotId);
                    if (! $pilot || ! $pilot->isPilot()) {
                        $errors['pilot_id'] = 'Pilot yang dipilih tidak sah.';
                    } elseif (! $pilot->is_active) {
                        $errors['pilot_id'] = "Pilot {$pilot->name} tidak aktif.";
                    } elseif ($conflict = $this->findPilotConflict($pilot->id, $lockedBooking->start_datetime, $lockedBooking->end_datetime, $lockedBooking->id)) {
                        $errors['pilot_id'] = "Pertindihan jadual! Pilot {$pilot->name} telah ditugaskan untuk tempahan {$conflict->booking_code} dari {$conflict->start_datetime->format('d/m/Y H:i')} hingga {$conflict->end_datetime->format('d/m/Y H:i')}.";
                    }
                }
            } else {
                $pilotId = null;
            }

            if (! empty($errors)) {
                throw ValidationException::withMessages($errors);
            }

            $lockedBooking->update([
                'vehicle_id' => $vehicleId,
                'pilot_id' => $pilotId,
                'status' => Booking::STATUS_APPROVED,
                'rejection_reason' => null,
                'rejection_remarks' => null,
                'approved_by' => $approver->id,
                'approved_at' => now(),
            ]);

            return $lockedBooking->fresh(['applicant', 'vehicle', 'pilot', 'approver']);
        });
    }

    /**
     * Complete a trip, validate odometer readings, and sync vehicle's current_odometer.
     *
     * @throws ValidationException
     */
    public function completeTrip(
        Booking $booking,
        ?int $odometerStart,
        ?int $odometerEnd,
        ?string $pilotRemarks = null
    ): Booking {
        return DB::transaction(function () use ($booking, $odometerStart, $odometerEnd, $pilotRemarks) {
            /** @var Booking $lockedBooking */
            $lockedBooking = Booking::query()->lockForUpdate()->findOrFail($booking->id);

            if (! $lockedBooking->isApproved()) {
                throw ValidationException::withMessages([
                    'status' => 'Hanya tempahan yang telah diluluskan boleh ditandakan sebagai selesai.',
                ]);
            }

            if ($lockedBooking->vehicle_id !== null || ($odometerStart !== null || $odometerEnd !== null)) {
                if ($odometerStart === null || $odometerEnd === null) {
                    throw ValidationException::withMessages([
                        'odometer_start' => 'Odometer Mula dan Odometer Tamat wajib diisi.',
                    ]);
                }

                if ($odometerEnd <= $odometerStart) {
                    throw ValidationException::withMessages([
                        'odometer_end' => 'Odometer Tamat mestilah lebih besar daripada Odometer Mula.',
                    ]);
                }
            }

            $lockedBooking->update([
                'odometer_start' => $odometerStart,
                'odometer_end' => $odometerEnd,
                'pilot_remarks' => $pilotRemarks,
                'status' => Booking::STATUS_COMPLETED,
            ]);

            if ($lockedBooking->vehicle_id && $odometerEnd !== null) {
                $vehicle = Vehicle::query()->lockForUpdate()->find($lockedBooking->vehicle_id);
                if ($vehicle) {
                    $vehicle->update([
                        'current_odometer' => $odometerEnd,
                    ]);
                }
            }

            return $lockedBooking->fresh(['applicant', 'vehicle', 'pilot', 'approver']);
        });
    }
}
