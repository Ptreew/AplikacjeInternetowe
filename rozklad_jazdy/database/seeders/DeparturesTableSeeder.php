<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
// Note: arrival_time is now calculated dynamically based on departure_time and route travel_time

class DeparturesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departures = [
            // Schedule 1: Warszawa - Kraków (dni robocze)
            // First stop - Warszawa Dworzec Centralny
            [
                'schedule_id' => 1,
                'stop_id' => 1,
                'vehicle_id' => 1,
                'departure_time' => '07:00:00',
                'price' => 99.00,
                'available_seats' => 350,  // Pojemność pojazdu EP09-042
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 1,
                'stop_id' => 1,
                'vehicle_id' => 1,
                'departure_time' => '09:30:00',
                'price' => 99.00,
                'available_seats' => 350,  // Pojemność pojazdu EP09-042
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 1,
                'stop_id' => 1,
                'vehicle_id' => 1,
                'departure_time' => '12:00:00',
                'price' => 99.00,
                'available_seats' => 350,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 1,
                'stop_id' => 1,
                'vehicle_id' => 1,
                'departure_time' => '15:30:00',
                'price' => 99.00,
                'available_seats' => 350,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 1,
                'stop_id' => 1,
                'vehicle_id' => 1,
                'departure_time' => '18:00:00',
                'price' => 99.00,
                'available_seats' => 350,  // Pojemność pojazdu EP09-042
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Schedule 2: Warszawa - Kraków (weekendy)
            [
                'schedule_id' => 2,
                'stop_id' => 1,
                'vehicle_id' => 2,
                'departure_time' => '08:00:00',
                'price' => 99.00,
                'available_seats' => 350,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 2,
                'stop_id' => 1,
                'vehicle_id' => 2,
                'departure_time' => '12:00:00',
                'price' => 99.00,
                'available_seats' => 350,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 2,
                'stop_id' => 1,
                'vehicle_id' => 2,
                'departure_time' => '16:00:00',
                'price' => 99.00,
                'available_seats' => 350,  // Pojemność pojazdu EP09-051
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Schedule 3: Kraków - Rzeszów (dni robocze)
            [
                'schedule_id' => 3,
                'stop_id' => 10,
                'vehicle_id' => 3,
                'departure_time' => '07:30:00',
                'price' => 99.00,
                'available_seats' => 45,  // Pojemność pojazdu ST-001
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 3,
                'stop_id' => 10,
                'vehicle_id' => 3,
                'departure_time' => '11:30:00',
                'price' => 99.00,
                'available_seats' => 45,  // Pojemność pojazdu ST-001
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 3,
                'stop_id' => 10,
                'vehicle_id' => 3,
                'departure_time' => '15:30:00',
                'price' => 99.00,
                'available_seats' => 45,  // Pojemność pojazdu ST-001
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Schedule 4: Kraków - Rzeszów (sobota)
            [
                'schedule_id' => 4,
                'stop_id' => 10,
                'vehicle_id' => 4,
                'departure_time' => '09:00:00',
                'price' => 99.00,
                'available_seats' => 45,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 4,
                'stop_id' => 10,
                'vehicle_id' => 4,
                'departure_time' => '15:00:00',
                'price' => 99.00,
                'available_seats' => 45,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Schedule 5: Warszawa - Lublin (dni robocze)
            [
                'schedule_id' => 5,
                'stop_id' => 2,
                'vehicle_id' => 5,
                'departure_time' => '08:00:00',
                'price' => 99.00,
                'available_seats' => 80,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 5,
                'stop_id' => 2,
                'vehicle_id' => 5,
                'departure_time' => '12:00:00',
                'price' => 5.00,
                'available_seats' => 80,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 5,
                'stop_id' => 2,
                'vehicle_id' => 5,
                'departure_time' => '16:00:00',
                'price' => 5.00,
                'available_seats' => 80,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // City bus schedules
            // Schedule 6: Line 175 (dni robocze) - Every 15 minutes during peak hours
            [
                'schedule_id' => 6,
                'stop_id' => 1,
                'vehicle_id' => 6,
                'departure_time' => '06:00:00',
                'price' => 5.00,
                'available_seats' => 80,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 6,
                'stop_id' => 1,
                'vehicle_id' => 6,
                'departure_time' => '06:15:00',
                'price' => 5.00,
                'available_seats' => 80,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 6,
                'stop_id' => 1,
                'vehicle_id' => 6,
                'departure_time' => '06:30:00',
                'price' => 5.00,
                'available_seats' => 80,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 6,
                'stop_id' => 1,
                'vehicle_id' => 6,
                'departure_time' => '06:45:00',
                'price' => 5.00,
                'available_seats' => 80,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 6,
                'stop_id' => 1,
                'vehicle_id' => 6,
                'departure_time' => '07:00:00',
                'price' => 5.00,
                'available_seats' => 80,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Schedule 7: Line 175 (sobota)
            [
                'schedule_id' => 7,
                'stop_id' => 1,
                'vehicle_id' => 6,
                'departure_time' => '07:00:00',
                'price' => 5.00,
                'available_seats' => 80,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 7,
                'stop_id' => 1,
                'vehicle_id' => 6,
                'departure_time' => '07:30:00',
                'price' => 5.00,
                'available_seats' => 80,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 7,
                'stop_id' => 1,
                'vehicle_id' => 6,
                'departure_time' => '08:00:00',
                'price' => 5.00,
                'available_seats' => 80,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Schedule 8: Line 12 (dni robocze)
            [
                'schedule_id' => 8,
                'stop_id' => 9,
                'vehicle_id' => 7,
                'departure_time' => '07:00:00',
                'price' => 5.00,
                'available_seats' => 85,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 8,
                'stop_id' => 9,
                'vehicle_id' => 7,
                'departure_time' => '07:20:00',
                'price' => 5.00,
                'available_seats' => 85,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 8,
                'stop_id' => 9,
                'vehicle_id' => 7,
                'departure_time' => '07:40:00',
                'price' => 5.00,
                'available_seats' => 85,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('departures')->insert($departures);
    }
}
