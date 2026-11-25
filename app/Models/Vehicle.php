<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    public const TYPES = [
        'Sedan',
        'SUV',
        'MPV',
        'Van',
        '4x4',
    ];

    public const STATUS_AVAILABLE = 'available';

    public const STATUS_MAINTENANCE = 'maintenance';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUSES = [
        self::STATUS_AVAILABLE => 'Tersedia (Available)',
        self::STATUS_MAINTENANCE => 'Penyelenggaraan (Maintenance)',
        self::STATUS_INACTIVE => 'Tidak Aktif (Inactive)',
    ];

    protected $fillable = [
        'plate_number',
        'model',
        'type',
        'capacity',
        'current_odometer',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'current_odometer' => 'integer',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function isUnderMaintenance(): bool
    {
        return $this->status === self::STATUS_MAINTENANCE;
    }

    public function displayName(): string
    {
        return "{$this->plate_number} - {$this->model}";
    }

    public function statusLabel(): string
    {
        return __(self::STATUSES[$this->status] ?? ucfirst($this->status));
    }

    /**
     * @return array<string, string>
     */
    public static function translatedStatuses(): array
    {
        return collect(self::STATUSES)->map(fn (string $label) => __($label))->all();
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            self::STATUS_MAINTENANCE => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            self::STATUS_INACTIVE => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
            default => 'bg-secondary-subtle text-secondary-emphasis',
        };
    }
}
