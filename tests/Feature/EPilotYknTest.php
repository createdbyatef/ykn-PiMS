<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EPilotYknTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $staff;

    private User $otherStaff;

    private User $pilot1;

    private User $pilot2;

    private Vehicle $availableVehicle;

    private Vehicle $maintenanceVehicle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create(['email' => 'admin@ykn.gov.my']);
        $this->staff = User::factory()->create(['email' => 'staff1@ykn.gov.my']);
        $this->otherStaff = User::factory()->create(['email' => 'staff2@ykn.gov.my']);
        $this->pilot1 = User::factory()->pilot()->create(['email' => 'pilot1@ykn.gov.my']);
        $this->pilot2 = User::factory()->pilot()->create(['email' => 'pilot2@ykn.gov.my']);

        $this->availableVehicle = Vehicle::create([
            'plate_number' => 'WYY 2048',
            'model' => 'Proton X70',
            'type' => 'SUV',
            'capacity' => 5,
            'current_odometer' => 45000,
            'status' => Vehicle::STATUS_AVAILABLE,
        ]);

        $this->maintenanceVehicle = Vehicle::create([
            'plate_number' => 'VHG 7741',
            'model' => 'Toyota Hilux',
            'type' => '4x4',
            'capacity' => 5,
            'current_odometer' => 70000,
            'status' => Vehicle::STATUS_MAINTENANCE,
        ]);
    }

    public function test_role_dashboards_and_access_control(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get(route('login'))->assertOk();

        // Admin dashboard
        $this->actingAs($this->admin)->get(route('dashboard'))->assertOk()->assertSee('Permohonan Menunggu');
        $this->actingAs($this->admin)->get(route('admin.vehicles.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.users.index'))->assertOk();

        // Staff dashboard & blocked from admin routes
        $this->actingAs($this->staff)->get(route('dashboard'))->assertOk()->assertSee('Permohonan Terkini');
        $this->actingAs($this->staff)->get(route('admin.vehicles.index'))->assertForbidden();
        $this->actingAs($this->staff)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($this->staff)->get(route('pilot.trips.index'))->assertForbidden();

        // Pilot redirected to mobile trips dashboard & blocked from admin/create
        $this->actingAs($this->pilot1)->get(route('dashboard'))->assertRedirect(route('pilot.trips.index'));
        $this->actingAs($this->pilot1)->get(route('pilot.trips.index'))->assertOk()->assertSee('Papan Pemuka Pilot HQ');
        $this->actingAs($this->pilot1)->get(route('bookings.create'))->assertForbidden();
    }

    public function test_three_day_notice_rule_and_self_drive_license_validation(): void
    {
        // Self-drive without driver_license_no must fail validation
        $this->actingAs($this->staff)
            ->post(route('bookings.store'), [
                'request_type' => Booking::TYPE_VEHICLE_ONLY,
                'start_datetime' => now()->addDays(5)->format('Y-m-d H:i:s'),
                'end_datetime' => now()->addDays(5)->addHours(4)->format('Y-m-d H:i:s'),
                'destination' => 'Putrajaya',
                'purpose' => 'Mesyuarat rasmi',
            ])
            ->assertSessionHasErrors('driver_license_no');

        // Booking within 3 days automatically flagged as is_urgent = true
        $this->actingAs($this->staff)
            ->post(route('bookings.store'), [
                'request_type' => Booking::TYPE_VEHICLE_AND_PILOT,
                'start_datetime' => now()->addDay()->format('Y-m-d H:i:s'),
                'end_datetime' => now()->addDay()->addHours(5)->format('Y-m-d H:i:s'),
                'destination' => 'PPS Hulu Langat',
                'purpose' => 'Bantuan kecemasan',
            ])
            ->assertRedirect();

        $urgentBooking = Booking::firstOrFail();
        $this->assertTrue($urgentBooking->is_urgent);
        $this->assertStringStartsWith('YKN-PLT-', $urgentBooking->booking_code);

        // Booking >= 3 days flagged as is_urgent = false
        $this->actingAs($this->staff)
            ->post(route('bookings.store'), [
                'request_type' => Booking::TYPE_VEHICLE_ONLY,
                'start_datetime' => now()->addDays(5)->format('Y-m-d H:i:s'),
                'end_datetime' => now()->addDays(5)->addHours(6)->format('Y-m-d H:i:s'),
                'destination' => 'Seremban',
                'purpose' => 'Audit aset',
                'driver_license_no' => '910412-08-5321',
            ])
            ->assertRedirect();

        $normalBooking = Booking::latest('id')->firstOrFail();
        $this->assertFalse($normalBooking->is_urgent);
        $this->assertSame('910412-08-5321', $normalBooking->driver_license_no);
    }

    public function test_conflict_prevention_blocks_overlapping_vehicle_pilot_and_maintenance(): void
    {
        $start = now()->addDays(4)->setTime(9, 0);
        $end = now()->addDays(4)->setTime(15, 0);

        $booking1 = Booking::create([
            'booking_code' => 'YKN-PLT-2026-0101',
            'user_id' => $this->staff->id,
            'request_type' => Booking::TYPE_VEHICLE_AND_PILOT,
            'start_datetime' => $start,
            'end_datetime' => $end,
            'destination' => 'Putrajaya',
            'purpose' => 'Mesyuarat 1',
            'is_urgent' => false,
            'status' => Booking::STATUS_PENDING,
        ]);

        // Assigning maintenance vehicle must be blocked
        $this->actingAs($this->admin)
            ->post(route('bookings.approve', $booking1), [
                'vehicle_id' => $this->maintenanceVehicle->id,
                'pilot_id' => $this->pilot1->id,
            ])
            ->assertSessionHasErrors('vehicle_id');

        // Approving with available vehicle & pilot succeeds
        $this->actingAs($this->admin)
            ->post(route('bookings.approve', $booking1), [
                'vehicle_id' => $this->availableVehicle->id,
                'pilot_id' => $this->pilot1->id,
            ])
            ->assertRedirect(route('bookings.show', $booking1));

        $this->assertSame(Booking::STATUS_APPROVED, $booking1->fresh()->status);

        // Overlapping booking (13:00 - 17:00 overlaps 09:00 - 15:00)
        $booking2 = Booking::create([
            'booking_code' => 'YKN-PLT-2026-0102',
            'user_id' => $this->otherStaff->id,
            'request_type' => Booking::TYPE_VEHICLE_AND_PILOT,
            'start_datetime' => $start->copy()->addHours(4),
            'end_datetime' => $end->copy()->addHours(2),
            'destination' => 'Shah Alam',
            'purpose' => 'Mesyuarat 2',
            'is_urgent' => false,
            'status' => Booking::STATUS_PENDING,
        ]);

        // Double-booking BOTH vehicle and pilot must fail
        $this->actingAs($this->admin)
            ->post(route('bookings.approve', $booking2), [
                'vehicle_id' => $this->availableVehicle->id,
                'pilot_id' => $this->pilot1->id,
            ])
            ->assertSessionHasErrors(['vehicle_id', 'pilot_id']);

        $this->assertSame(Booking::STATUS_PENDING, $booking2->fresh()->status);

        // Adjacent non-overlapping slot (15:00 - 18:00) succeeds!
        $booking3 = Booking::create([
            'booking_code' => 'YKN-PLT-2026-0103',
            'user_id' => $this->otherStaff->id,
            'request_type' => Booking::TYPE_VEHICLE_AND_PILOT,
            'start_datetime' => $end->copy(),
            'end_datetime' => $end->copy()->addHours(3),
            'destination' => 'Klang',
            'purpose' => 'Lawatan petang',
            'is_urgent' => false,
            'status' => Booking::STATUS_PENDING,
        ]);

        $this->actingAs($this->admin)
            ->post(route('bookings.approve', $booking3), [
                'vehicle_id' => $this->availableVehicle->id,
                'pilot_id' => $this->pilot1->id,
            ])
            ->assertRedirect(route('bookings.show', $booking3));

        $this->assertSame(Booking::STATUS_APPROVED, $booking3->fresh()->status);
    }

    public function test_pilot_odometer_completion_validates_and_syncs_vehicle_odometer(): void
    {
        $booking = Booking::create([
            'booking_code' => 'YKN-PLT-2026-0201',
            'user_id' => $this->staff->id,
            'request_type' => Booking::TYPE_VEHICLE_AND_PILOT,
            'vehicle_id' => $this->availableVehicle->id,
            'pilot_id' => $this->pilot1->id,
            'start_datetime' => now()->subHours(3),
            'end_datetime' => now()->addHour(),
            'destination' => 'Hospital Serdang',
            'purpose' => 'Pemantauan Anjung Kasih',
            'is_urgent' => false,
            'status' => Booking::STATUS_APPROVED,
            'approved_by' => $this->admin->id,
            'approved_at' => now()->subDays(2),
        ]);

        // Invalid odometer_end <= odometer_start must fail
        $this->actingAs($this->pilot1)
            ->post(route('bookings.complete', $booking), [
                'odometer_start' => 45000,
                'odometer_end' => 44990,
            ])
            ->assertSessionHasErrors('odometer_end');

        // Unassigned pilot cannot complete someone else's trip
        $this->actingAs($this->pilot2)
            ->post(route('bookings.complete', $booking), [
                'odometer_start' => 45000,
                'odometer_end' => 45125,
            ])
            ->assertForbidden();

        // Valid completion updates booking and syncs vehicle.current_odometer
        $this->actingAs($this->pilot1)
            ->post(route('bookings.complete', $booking), [
                'odometer_start' => 45000,
                'odometer_end' => 45125,
                'pilot_remarks' => 'Perjalanan selamat dan lancar.',
            ])
            ->assertRedirect(route('pilot.trips.index'));

        $booking->refresh();
        $this->availableVehicle->refresh();

        $this->assertSame(Booking::STATUS_COMPLETED, $booking->status);
        $this->assertSame(125, $booking->totalDistance());
        $this->assertSame(45125, $this->availableVehicle->current_odometer);
    }

    public function test_official_pdf_generation_and_memo_restrictions(): void
    {
        // Approved self-drive booking
        $selfDriveBooking = Booking::create([
            'booking_code' => 'YKN-PLT-2026-0301',
            'user_id' => $this->staff->id,
            'request_type' => Booking::TYPE_VEHICLE_ONLY,
            'vehicle_id' => $this->availableVehicle->id,
            'pilot_id' => null,
            'start_datetime' => now()->addDays(4),
            'end_datetime' => now()->addDays(5),
            'destination' => 'Seremban',
            'purpose' => 'Audit kewangan',
            'driver_license_no' => '910412-08-5321',
            'is_urgent' => false,
            'status' => Booking::STATUS_APPROVED,
            'approved_by' => $this->admin->id,
            'approved_at' => now(),
        ]);

        // Borang Kenderaan PDF works for applicant
        $borangRes = $this->actingAs($this->staff)->get(route('bookings.pdf.borang', $selfDriveBooking));
        $borangRes->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $borangRes->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF-', $borangRes->getContent());

        // Memo Kebenaran Memandu PDF works for approved vehicle_only
        $memoRes = $this->actingAs($this->staff)->get(route('bookings.pdf.memo', $selfDriveBooking));
        $memoRes->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $memoRes->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF-', $memoRes->getContent());

        // Another staff cannot download this applicant's PDF
        $this->actingAs($this->otherStaff)
            ->get(route('bookings.pdf.memo', $selfDriveBooking))
            ->assertForbidden();

        // Rejected booking: Borang PDF works, Memo PDF forbidden
        $rejectedBooking = Booking::create([
            'booking_code' => 'YKN-PLT-2026-0302',
            'user_id' => $this->staff->id,
            'request_type' => Booking::TYPE_VEHICLE_ONLY,
            'start_datetime' => now()->addDays(4),
            'end_datetime' => now()->addDays(4)->addHours(4),
            'destination' => 'Melaka',
            'purpose' => 'Tinjauan tapak',
            'driver_license_no' => '910412-08-5321',
            'is_urgent' => false,
            'status' => Booking::STATUS_REJECTED,
            'rejection_reason' => Booking::REJECTION_MAINTENANCE,
            'rejection_remarks' => 'Kenderaan diservis',
            'approved_by' => $this->admin->id,
            'approved_at' => now(),
        ]);

        $this->actingAs($this->admin)->get(route('bookings.pdf.borang', $rejectedBooking))->assertOk();
        $this->actingAs($this->admin)->get(route('bookings.pdf.memo', $rejectedBooking))->assertForbidden();
    }

    public function test_calendar_events_api_and_admin_crud(): void
    {
        Booking::create([
            'booking_code' => 'YKN-PLT-2026-0401',
            'user_id' => $this->staff->id,
            'request_type' => Booking::TYPE_VEHICLE_AND_PILOT,
            'vehicle_id' => $this->availableVehicle->id,
            'pilot_id' => $this->pilot1->id,
            'start_datetime' => now()->addDays(2),
            'end_datetime' => now()->addDays(2)->addHours(4),
            'destination' => 'KPWKM Putrajaya',
            'purpose' => 'Mesyuarat',
            'status' => Booking::STATUS_APPROVED,
            'approved_by' => $this->admin->id,
            'approved_at' => now(),
        ]);

        $this->actingAs($this->staff)
            ->get(route('calendar.index'))
            ->assertOk();

        $this->actingAs($this->staff)
            ->getJson(route('calendar.events', [
                'start' => now()->subDays(5)->toIso8601String(),
                'end' => now()->addDays(15)->toIso8601String(),
            ]))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.extendedProps.booking_code', 'YKN-PLT-2026-0401');

        // Admin creates a new vehicle
        $this->actingAs($this->admin)
            ->post(route('admin.vehicles.store'), [
                'plate_number' => 'VXX 9090',
                'model' => 'Perodua Alza AV',
                'type' => 'MPV',
                'capacity' => 7,
                'current_odometer' => 12000,
                'status' => Vehicle::STATUS_AVAILABLE,
            ])
            ->assertRedirect(route('admin.vehicles.index'));

        $this->assertDatabaseHas('vehicles', ['plate_number' => 'VXX 9090']);

        // Admin creates a new pilot user
        $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Encik Kamal Pilot',
                'email' => 'kamal.pilot@ykn.gov.my',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => User::ROLE_PILOT,
                'jawatan' => 'Pilot HQ',
                'unit' => 'Unit Pentadbiran',
                'phone' => '012-3344556',
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'kamal.pilot@ykn.gov.my',
            'role' => User::ROLE_PILOT,
        ]);
    }

    public function test_admin_rejection_flow_inactive_login_block_and_seeder_counts(): void
    {
        $pending = Booking::create([
            'booking_code' => 'YKN-PLT-2026-0501',
            'user_id' => $this->staff->id,
            'request_type' => Booking::TYPE_VEHICLE_AND_PILOT,
            'start_datetime' => now()->addDays(4),
            'end_datetime' => now()->addDays(4)->addHours(4),
            'destination' => 'Putrajaya',
            'purpose' => 'Mesyuarat',
            'status' => Booking::STATUS_PENDING,
        ]);

        // Rejection reason 'other' requires remarks
        $this->actingAs($this->admin)
            ->post(route('bookings.reject', $pending), [
                'rejection_reason' => Booking::REJECTION_OTHER,
                'rejection_remarks' => '',
            ])
            ->assertSessionHasErrors('rejection_remarks');

        // Valid rejection succeeds
        $this->actingAs($this->admin)
            ->post(route('bookings.reject', $pending), [
                'rejection_reason' => Booking::REJECTION_ALL_IN_USE,
                'rejection_remarks' => 'Semua kenderaan terlibat program rasmi luar daerah.',
            ])
            ->assertRedirect(route('bookings.show', $pending));

        $pending->refresh();
        $this->assertSame(Booking::STATUS_REJECTED, $pending->status);
        $this->assertSame(Booking::REJECTION_ALL_IN_USE, $pending->rejection_reason);

        // Render key form and detail views without errors
        $this->actingAs($this->staff)->get(route('bookings.create'))->assertOk();
        $this->actingAs($this->staff)->get(route('bookings.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('bookings.show', $pending))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.vehicles.create'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.vehicles.edit', $this->availableVehicle))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.users.create'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.users.edit', $this->pilot1))->assertOk();

        // Inactive user cannot log in
        auth()->logout();
        $inactive = User::factory()->create(['email' => 'inactive@ykn.gov.my', 'is_active' => false]);
        $this->post(route('login.attempt'), [
            'email' => $inactive->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
