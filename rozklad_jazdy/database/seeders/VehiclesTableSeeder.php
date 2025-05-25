<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehiclesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            // PolBus vehicles
            [
                'line_id' => 1, // Warszawa - Kraków line
                'vehicle_number' => 'PB-1234',
                'type' => 'Coach',
                'capacity' => 52,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 1, // Warszawa - Kraków line
                'vehicle_number' => 'PB-5678',
                'type' => 'Coach',
                'capacity' => 48,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 2, // Kraków - Rzeszów line
                'vehicle_number' => 'PB-9012',
                'type' => 'Coach',
                'capacity' => 52,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Szybki Transport vehicles
            [
                'line_id' => 3, // Warszawa - Lublin line
                'vehicle_number' => 'ST-001',
                'type' => 'Coach',
                'capacity' => 45,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 3, // Warszawa - Lublin line
                'vehicle_number' => 'ST-002',
                'type' => 'Coach',
                'capacity' => 45,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Miejskie Linie Komunikacyjne vehicles
            [
                'line_id' => 4, // Line 175
                'vehicle_number' => 'MLK-175-01',
                'type' => 'City Bus',
                'capacity' => 80,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 4, // Line 175
                'vehicle_number' => 'MLK-175-02',
                'type' => 'City Bus',
                'capacity' => 80,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 5, // Line 12
                'vehicle_number' => 'MLK-012-01',
                'type' => 'City Bus',
                'capacity' => 85,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 6, // Line 22
                'vehicle_number' => 'MLK-022-01',
                'type' => 'City Bus',
                'capacity' => 70,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 7, // Line 501
                'vehicle_number' => 'MLK-501-01',
                'type' => 'Articulated Bus',
                'capacity' => 120,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('vehicles')->insert($vehicles);
    }
}
