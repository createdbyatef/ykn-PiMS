<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with clean initial YKN accounts and fleet vehicles.
     * Bookings start empty so requests can be tested cleanly from scratch.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Admin (Unit Pentadbiran)
        User::updateOrCreate(
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

        // 2. Staff YKN
        User::updateOrCreate(
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

        // 3. HQ Pilots
        User::updateOrCreate(
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

        User::updateOrCreate(
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

        // 4. 5 YKN Vehicles
        Vehicle::updateOrCreate(
            ['plate_number' => 'WYY 2048'],
            [
                'model' => 'Proton X70 1.8 TGDi Executive',
                'type' => 'SUV',
                'capacity' => 5,
                'current_odometer' => 48250,
                'status' => Vehicle::STATUS_AVAILABLE,
            ]
        );

        Vehicle::updateOrCreate(
            ['plate_number' => 'VJM 8812'],
            [
                'model' => 'Toyota Innova Zenix 2.0',
                'type' => 'MPV',
                'capacity' => 7,
                'current_odometer' => 31420,
                'status' => Vehicle::STATUS_AVAILABLE,
            ]
        );

        Vehicle::updateOrCreate(
            ['plate_number' => 'WVA 5519'],
            [
                'model' => 'Honda Civic 1.5 RS',
                'type' => 'Sedan',
                'capacity' => 5,
                'current_odometer' => 62100,
                'status' => Vehicle::STATUS_AVAILABLE,
            ]
        );

        Vehicle::updateOrCreate(
            ['plate_number' => 'VKD 3304'],
            [
                'model' => 'Toyota Hiace High Roof',
                'type' => 'Van',
                'capacity' => 12,
                'current_odometer' => 85940,
                'status' => Vehicle::STATUS_AVAILABLE,
            ]
        );

        Vehicle::updateOrCreate(
            ['plate_number' => 'VHG 7741'],
            [
                'model' => 'Toyota Hilux 2.8 Rogue',
                'type' => '4x4',
                'capacity' => 5,
                'current_odometer' => 74300,
                'status' => Vehicle::STATUS_MAINTENANCE,
            ]
        );
    }
}
 