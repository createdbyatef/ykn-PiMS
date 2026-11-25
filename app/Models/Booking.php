<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Booking extends Model
{
    use HasFactory;

    public const TYPE_VEHICLE_AND_PILOT = 'vehicle_and_pilot';

    public const TYPE_VEHICLE_ONLY = 'vehicle_only';

    public const TYPE_PILOT_ONLY = 'pilot_only';

    public const REQUEST_TYPES = [
        self::TYPE_VEHICLE_AND_PILOT => 'Kenderaan + Pilot',
        self::TYPE_VEHICLE_ONLY => 'Kenderaan Sahaja (Pandu Sendiri)',
        self::TYPE_PILOT_ONLY => 'Pilot Sahaja',
    ];

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        self::STATUS_PENDING => 'Menunggu Kelulusan',
        self::STATUS_APPROVED => 'Diluluskan',
        self::STATUS_REJECTED => 'Tidak Diluluskan',
        self::STATUS_COMPLETED => 'Selesai',
    ];

    public const REJECTION_ALL_IN_USE = 'all_vehicles_in_use';

    public const REJECTION_ALREADY_BOOKED = 'vehicle_already_booked';

    public const REJECTION_MAINTENANCE = 'vehicle_under_maintenance';

    public const REJECTION_OTHER = 'other';

    public const REJECTION_REASONS = [
        self::REJECTION_ALL_IN_USE => 'Kesemua kenderaan digunakan',
        self::REJECTION_ALREADY_BOOKED => 'Kenderaan telah ditempah',
        self::REJECTION_MAINTENANCE => 'Kenderaan sedang diselenggara',
        self::REJECTION_OTHER => 'Lain-lain',
    ];

    protected $fillable = [
        'booking_code',
        'user_id',
        'request_type',
        'vehicle_id',
        'pilot_id',
        'start_datetime',
        'end_datetime',
        'destination',
        'purpose',
        'driver_license_no',
        'is_urgent',
        'status',
        'rejection_reason',
        'rejection_remarks',
        'approved_by',
        'approved_at',
        'odometer_start',
        'odometer_end',
        'pilot_remarks',
    ];

    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
            'approved_at' => 'datetime',
            'is_urgent' => 'boolean',
            'odometer_start' => 'integer',
            'odometer_end' => 'integer',
        ];
    }

    /**
     * Generate the next sequential unique booking code (e.g. YKN-PLT-2026-0001).
     */
    public static function generateBookingCode(?int $year = null): string
    {
        $year = $year ?? (int) now()->format('Y');
        $prefix = "YKN-PLT-{$year}-";

        $lastCode = DB::table('bookings')
            ->where('booking_code', 'like', $prefix.'%')
            ->orderByDesc('booking_code')
            ->value('booking_code');

        $nextNumber = 1;
        if ($lastCode) {
            $numericPart = (int) substr($lastCode, strlen($prefix));
            $nextNumber = $numericPart + 1;
        }

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function pilot(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pilot_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function requiresVehicle(): bool
    {
        return in_array($this->request_type, [
            self::TYPE_VEHICLE_AND_PILOT,
            self::TYPE_VEHICLE_ONLY,
        ], true);
    }

    public function requiresPilot(): bool
    {
        return in_array($this->request_type, [
            self::TYPE_VEHICLE_AND_PILOT,
            self::TYPE_PILOT_ONLY,
        ], true);
    }

    public function isSelfDrive(): bool
    {
        return $this->request_type === self::TYPE_VEHICLE_ONLY;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Official self-drive memo is enabled only for approved vehicle_only bookings.
     */
    public function canDownloadMemo(): bool
    {
        return $this->request_type === self::TYPE_VEHICLE_ONLY
            && $this->status === self::STATUS_APPROVED;
    }

    public function totalDistance(): ?int
    {
        if ($this->odometer_start === null || $this->odometer_end === null) {
            return null;
        }

        return max(0, $this->odometer_end - $this->odometer_start);
    }

    public function requestTypeLabel(): string
    {
        return __(self::REQUEST_TYPES[$this->request_type] ?? $this->request_type);
    }

    public function statusLabel(): string
    {
        return __(self::STATUSES[$this->status] ?? ucfirst($this->status));
    }

    public function rejectionReasonLabel(): ?string
    {
        if (! $this->rejection_reason) {
            return null;
        }

        return __(self::REJECTION_REASONS[$this->rejection_reason] ?? $this->rejection_reason);
    }

    /**
     * @return array<string, string>
     */
    public static function translatedRequestTypes(): array
    {
        return collect(self::REQUEST_TYPES)->map(fn (string $label) => __($label))->all();
    }

    /**
     * @return array<string, string>
     */
    public static function translatedStatuses(): array
    {
        return collect(self::STATUSES)->map(fn (string $label) => __($label))->all();
    }

    /**
     * @return array<string, string>
     */
    public static function translatedRejectionReasons(): array
    {
        return collect(self::REJECTION_REASONS)->map(fn (string $label) => __($label))->all();
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            self::STATUS_APPROVED => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
            self::STATUS_REJECTED => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
            self::STATUS_COMPLETED => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            default => 'bg-secondary-subtle text-secondary-emphasis',
        };
    }

    public function requestTypeBadgeClass(): string
    {
        return match ($this->request_type) {
            self::TYPE_VEHICLE_AND_PILOT => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            self::TYPE_VEHICLE_ONLY => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
            self::TYPE_PILOT_ONLY => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            default => 'bg-light text-dark',
        };
    }
}
