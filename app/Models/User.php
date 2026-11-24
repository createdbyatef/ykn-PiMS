<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_STAFF = 'staff';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_PILOT = 'pilot';

    public const ROLES = [
        self::ROLE_STAFF => 'Staf YKN',
        self::ROLE_ADMIN => 'Unit Pentadbiran (Admin)',
        self::ROLE_PILOT => 'Pilot HQ',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'jawatan',
        'unit',
        'phone',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Bookings submitted by this user as applicant.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    /**
     * Trips assigned to this user as a Pilot.
     */
    public function assignedTrips(): HasMany
    {
        return $this->hasMany(Booking::class, 'pilot_id');
    }

    /**
     * Bookings approved or processed by this Admin.
     */
    public function approvedBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'approved_by');
    }

    public function scopePilots(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_PILOT);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function isPilot(): bool
    {
        return $this->role === self::ROLE_PILOT;
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    public function roleBadgeClass(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
            self::ROLE_PILOT => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            default => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
        };
    }
}
