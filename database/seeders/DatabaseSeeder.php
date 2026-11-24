<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic Yayasan Kebajikan Negara (YKN) data.
     * Includes: 1 Admin, 3 Staff, 4 HQ Pilots, 5 YKN Vehicles, and realistic sample bookings.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Admin (Unit Pentadbiran)
        $admin = User::updateOrCreate(
            ['email' => 'admin@ykn.gov.my'],
            [
                'name' => 'Encik Azman bin Razak',
                'password' => $password,
                'role' => User::ROLE_ADMIN,
                'jawatan' => 'Pegawai Tadbir Kanan',
                'unit' => 'Unit Pentadbiran',
                'phone' => '013-2204810',
                'is_active' => true,
            ]
        );

        // 2. 3 YKN Staff
        $staff1 = User::updateOrCreate(
            ['email' => 'huda@ykn.gov.my'],
            [
                'name' => 'Puan Nurul Huda binti Ismail',
                'password' => $password,
                'role' => User::ROLE_STAFF,
                'jawatan' => 'Pegawai Kebajikan Masyarakat',
                'unit' => 'Bahagian Operasi & Program',
                'phone' => '019-3345102',
                'is_active' => true,
            ]
        );

        $staff2 = User::updateOrCreate(
            ['email' => 'hafiz@ykn.gov.my'],
            [
                'name' => 'Encik Hafizuddin bin Salleh',
                'password' => $password,
                'role' => User::ROLE_STAFF,
                'jawatan' => 'Penolong Pegawai Kewangan',
                'unit' => 'Unit Kewangan & Akaun',
                'phone' => '017-6621940',
                'is_active' => true,
            ]
        );

        $staff3 = User::updateOrCreate(
            ['email' => 'aisyah@ykn.gov.my'],
            [
                'name' => 'Cik Aisyah binti Kamarudin',
                'password' => $password,
                'role' => User::ROLE_STAFF,
                'jawatan' => 'Pegawai Komunikasi Korporat',
                'unit' => 'Unit Komunikasi Korporat',
                'phone' => '012-7789215',
                'is_active' => true,
            ]
        );

        // 3. 4 HQ Pilots
        $pilot1 = User::updateOrCreate(
            ['email' => 'roslan.pilot@ykn.gov.my'],
            [
                'name' => 'Encik Roslan bin Ahmad',
                'password' => $password,
                'role' => User::ROLE_PILOT,
                'jawatan' => 'Pilot Kanan HQ',
                'unit' => 'Unit Pentadbiran',
                'phone' => '019-2188401',
                'is_active' => true,
            ]
        );

        $pilot2 = User::updateOrCreate(
            ['email' => 'zulkifli.pilot@ykn.gov.my'],
            [
                'name' => 'Encik Zulkifli bin Hassan',
                'password' => $password,
                'role' => User::ROLE_PILOT,
                'jawatan' => 'Pilot HQ',
                'unit' => 'Unit Pentadbiran',
                'phone' => '016-4490123',
                'is_active' => true,
            ]
        );

        $pilot3 = User::updateOrCreate(
            ['email' => 'faizal.pilot@ykn.gov.my'],
            [
                'name' => 'Encik Mohd Faizal bin Othman',
                'password' => $password,
                'role' => User::ROLE_PILOT,
                'jawatan' => 'Pilot HQ',
                'unit' => 'Unit Pentadbiran',
                'phone' => '013-9083321',
                'is_active' => true,
            ]
        );

        $pilot4 = User::updateOrCreate(
            ['email' => 'syahrul.pilot@ykn.gov.my'],
            [
                'name' => 'Encik Syahrul Nizam bin Yusof',
                'password' => $password,
                'role' => User::ROLE_PILOT,
                'jawatan' => 'Pilot HQ',
                'unit' => 'Unit Pentadbiran',
                'phone' => '011-28450912',
                'is_active' => true,
            ]
        );

        // 4. 5 YKN Vehicles
        $v1 = Vehicle::updateOrCreate(
            ['plate_number' => 'WYY 2048'],
            [
                'model' => 'Proton X70 1.8 TGDi Executive',
                'type' => 'SUV',
                'capacity' => 5,
                'current_odometer' => 48250,
                'status' => Vehicle::STATUS_AVAILABLE,
            ]
        );

        $v2 = Vehicle::updateOrCreate(
            ['plate_number' => 'VJM 8812'],
            [
                'model' => 'Toyota Innova Zenix 2.0',
                'type' => 'MPV',
                'capacity' => 7,
                'current_odometer' => 31420,
                'status' => Vehicle::STATUS_AVAILABLE,
            ]
        );

        $v3 = Vehicle::updateOrCreate(
            ['plate_number' => 'WVA 5519'],
            [
                'model' => 'Honda Civic 1.5 RS',
                'type' => 'Sedan',
                'capacity' => 5,
                'current_odometer' => 62100,
                'status' => Vehicle::STATUS_AVAILABLE,
            ]
        );

        $v4 = Vehicle::updateOrCreate(
            ['plate_number' => 'VKD 3304'],
            [
                'model' => 'Toyota Hiace High Roof',
                'type' => 'Van',
                'capacity' => 12,
                'current_odometer' => 85940,
                'status' => Vehicle::STATUS_AVAILABLE,
            ]
        );

        $v5 = Vehicle::updateOrCreate(
            ['plate_number' => 'VHG 7741'],
            [
                'model' => 'Toyota Hilux 2.8 Rogue',
                'type' => '4x4',
                'capacity' => 5,
                'current_odometer' => 74300,
                'status' => Vehicle::STATUS_MAINTENANCE,
            ]
        );

        // 5. Realistic Sample Bookings (idempotent via booking_code)
        $year = now()->format('Y');
        $today = now()->startOfDay();

        // Booking 1: Completed trip with odometer logs
        Booking::updateOrCreate(
            ['booking_code' => "YKN-PLT-{$year}-0001"],
            [
                'user_id' => $staff1->id,
                'request_type' => Booking::TYPE_VEHICLE_AND_PILOT,
                'vehicle_id' => $v2->id,
                'pilot_id' => $pilot1->id,
                'start_datetime' => $today->copy()->subDays(3)->setTime(8, 30),
                'end_datetime' => $today->copy()->subDays(3)->setTime(17, 0),
                'destination' => 'Anjung Kasih YKN, Hospital Serdang, Selangor',
                'purpose' => 'Lawatan pemantauan operasi dan penyerahan bekalan keperluan asas penghuni Anjung Kasih.',
                'driver_license_no' => null,
                'is_urgent' => false,
                'status' => Booking::STATUS_COMPLETED,
                'approved_by' => $admin->id,
                'approved_at' => $today->copy()->subDays(7)->setTime(10, 15),
                'odometer_start' => 31342,
                'odometer_end' => 31420,
                'pilot_remarks' => 'Perjalanan lancar, kenderaan dikembalikan dalam keadaan bersih dan tangki minyak penuh.',
            ]
        );

        // Booking 2: Active Approved Outstation Trip (Today) - Vehicle + Pilot
        Booking::updateOrCreate(
            ['booking_code' => "YKN-PLT-{$year}-0002"],
            [
                'user_id' => $staff3->id,
                'request_type' => Booking::TYPE_VEHICLE_AND_PILOT,
                'vehicle_id' => $v1->id,
                'pilot_id' => $pilot1->id,
                'start_datetime' => $today->copy()->setTime(8, 0),
                'end_datetime' => $today->copy()->setTime(18, 0),
                'destination' => 'Kementerian Pembangunan Wanita, Keluarga dan Masyarakat (KPWKM), Putrajaya',
                'purpose' => 'Menghadiri Mesyuarat Penyelarasan Program Kebajikan Nasional bersama Pengurusan Tertinggi KPWKM.',
                'driver_license_no' => null,
                'is_urgent' => false,
                'status' => Booking::STATUS_APPROVED,
                'approved_by' => $admin->id,
                'approved_at' => $today->copy()->subDays(4)->setTime(14, 20),
            ]
        );

        // Booking 3: Approved Self-Drive (vehicle_only) - Enables Memo Kebenaran Memandu PDF!
        Booking::updateOrCreate(
            ['booking_code' => "YKN-PLT-{$year}-0003"],
            [
                'user_id' => $staff2->id,
                'request_type' => Booking::TYPE_VEHICLE_ONLY,
                'vehicle_id' => $v3->id,
                'pilot_id' => null,
                'start_datetime' => $today->copy()->addDays(2)->setTime(9, 0),
                'end_datetime' => $today->copy()->addDays(3)->setTime(17, 30),
                'destination' => 'Anjung Kasih YKN, Hospital Tuanku Ja\'afar, Seremban',
                'purpose' => 'Audit dalaman kewangan dan semakan inventori aset suku keempat YKN.',
                'driver_license_no' => '910412-08-5321 (Kelas D)',
                'is_urgent' => false,
                'status' => Booking::STATUS_APPROVED,
                'approved_by' => $admin->id,
                'approved_at' => $today->copy()->subDays(2)->setTime(11, 0),
            ]
        );

        // Booking 4: Approved Pilot Only (Upcoming)
        Booking::updateOrCreate(
            ['booking_code' => "YKN-PLT-{$year}-0004"],
            [
                'user_id' => $staff1->id,
                'request_type' => Booking::TYPE_PILOT_ONLY,
                'vehicle_id' => null,
                'pilot_id' => $pilot2->id,
                'start_datetime' => $today->copy()->addDays(4)->setTime(8, 30),
                'end_datetime' => $today->copy()->addDays(4)->setTime(16, 30),
                'destination' => 'Pusat Transit Gelandangan Kuala Lumpur',
                'purpose' => 'Tugasan pemanduan kenderaan rasmi rakan strategik bagi Program Santuni Komuniti YKN.',
                'driver_license_no' => null,
                'is_urgent' => false,
                'status' => Booking::STATUS_APPROVED,
                'approved_by' => $admin->id,
                'approved_at' => $today->copy()->subDay()->setTime(15, 45),
            ]
        );

        // Booking 5: Pending Urgent Request (less than 3 days notice)
        Booking::updateOrCreate(
            ['booking_code' => "YKN-PLT-{$year}-0005"],
            [
                'user_id' => $staff1->id,
                'request_type' => Booking::TYPE_VEHICLE_AND_PILOT,
                'vehicle_id' => null,
                'pilot_id' => null,
                'start_datetime' => $today->copy()->addDay()->setTime(9, 0),
                'end_datetime' => $today->copy()->addDay()->setTime(17, 0),
                'destination' => 'Pusat Pemindahan Sementara (PPS) Hulu Langat, Selangor',
                'purpose' => 'Penghantaran segera kit bantuan bencana banjir dan barangan keperluan asas mangsa.',
                'driver_license_no' => null,
                'is_urgent' => true,
                'status' => Booking::STATUS_PENDING,
            ]
        );

        // Booking 6: Pending Standard Self-Drive Request (more than 3 days notice)
        Booking::updateOrCreate(
            ['booking_code' => "YKN-PLT-{$year}-0006"],
            [
                'user_id' => $staff3->id,
                'request_type' => Booking::TYPE_VEHICLE_ONLY,
                'vehicle_id' => null,
                'pilot_id' => null,
                'start_datetime' => $today->copy()->addDays(6)->setTime(8, 30),
                'end_datetime' => $today->copy()->addDays(6)->setTime(17, 30),
                'destination' => 'Shah Alam Convention Centre (SACC), Selangor',
                'purpose' => 'Liputan media, pemasangan reruai pameran dan sesi fotografi rasmi Majlis Makan Malam Amal YKN.',
                'driver_license_no' => '940825-14-6108 (Kelas D)',
                'is_urgent' => false,
                'status' => Booking::STATUS_PENDING,
            ]
        );

        // Booking 7: Rejected Request (demonstrates official rejection checkboxes on Borang PDF)
        Booking::updateOrCreate(
            ['booking_code' => "YKN-PLT-{$year}-0007"],
            [
                'user_id' => $staff2->id,
                'request_type' => Booking::TYPE_VEHICLE_AND_PILOT,
                'vehicle_id' => null,
                'pilot_id' => null,
                'start_datetime' => $today->copy()->subDay()->setTime(8, 0),
                'end_datetime' => $today->copy()->subDay()->setTime(17, 0),
                'destination' => 'Anjung Kasih YKN, Hospital Melaka',
                'purpose' => 'Tinjauan tapak penyelenggaraan sistem penyaman udara.',
                'driver_license_no' => null,
                'is_urgent' => true,
                'status' => Booking::STATUS_REJECTED,
                'rejection_reason' => Booking::REJECTION_MAINTENANCE,
                'rejection_remarks' => 'Kenderaan 4x4 (VHG 7741) sedang menjalani servis berkala di bengkel rasmi.',
                'approved_by' => $admin->id,
                'approved_at' => $today->copy()->subDays(2)->setTime(16, 10),
            ]
        );
    }
}
