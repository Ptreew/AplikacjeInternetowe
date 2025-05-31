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
                'type' => 'Autokar',
                'capacity' => 52,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 1, // Warszawa - Kraków line
                'vehicle_number' => 'PB-5678',
                'type' => 'Autokar',
                'capacity' => 48,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 2, // Kraków - Rzeszów line
                'vehicle_number' => 'PB-9012',
                'type' => 'Autokar',
                'capacity' => 52,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Szybki Transport vehicles
            [
                'line_id' => 3, // Warszawa - Lublin line
                'vehicle_number' => 'ST-001',
                'type' => 'Autokar',
                'capacity' => 45,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 3, // Warszawa - Lublin line
                'vehicle_number' => 'ST-002',
                'type' => 'Autokar',
                'capacity' => 45,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Miejskie Linie Komunikacyjne vehicles
            [
                'line_id' => 4, // Line 175
                'vehicle_number' => 'MLK-175-01',
                'type' => 'Autobus miejski',
                'capacity' => 80,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 4, // Line 175
                'vehicle_number' => 'MLK-175-02',
                'type' => 'Autobus miejski',
                'capacity' => 80,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 5, // Line 12
                'vehicle_number' => 'MLK-012-01',
                'type' => 'Autobus miejski',
                'capacity' => 85,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 6, // Line 22
                'vehicle_number' => 'MLK-022-01',
                'type' => 'Autobus miejski',
                'capacity' => 70,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 7, // Line 501
                'vehicle_number' => 'MLK-501-01',
                'type' => 'Autobus przegubowy',
                'capacity' => 120,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // PKP Intercity trains
            [
                'line_id' => 8, // IC 3100 (Warszawa - Gdańsk)
                'vehicle_number' => 'EP09-001',
                'type' => 'Pociąg ekspresowy',
                'capacity' => 350,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 9, // IC 1400 (Warszawa - Poznań)
                'vehicle_number' => 'EP09-012',
                'type' => 'Pociąg ekspresowy',
                'capacity' => 320,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 10, // IC 5400 (Kraków - Wrocław)
                'vehicle_number' => 'EP07-105',
                'type' => 'Pociąg ekspresowy',
                'capacity' => 330,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Koleje Regionalne trains
            [
                'line_id' => 11, // R 10234 (Warszawa - Radom)
                'vehicle_number' => 'EN57-1001',
                'type' => 'Pociąg regionalny',
                'capacity' => 240,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 11, // R 10234 (Warszawa - Radom) - drugi skład
                'vehicle_number' => 'EN57-1002',
                'type' => 'Pociąg regionalny',
                'capacity' => 240,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'line_id' => 12, // R 12345 (Kraków - Kielce)
                'vehicle_number' => 'EN71-045',
                'type' => 'Pociąg regionalny',
                'capacity' => 260,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('vehicles')->insert($vehicles);
    }
}
