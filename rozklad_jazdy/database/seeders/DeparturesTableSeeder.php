<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeparturesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departures = [
            // Schedule 1: Warszawa - Kraków (weekday)
            // First stop - Warszawa Dworzec Centralny
            [
                'schedule_id' => 1,
                'stop_id' => 1,
                'departure_time' => '07:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 1,
                'stop_id' => 1,
                'departure_time' => '09:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 1,
                'stop_id' => 1,
                'departure_time' => '12:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 1,
                'stop_id' => 1,
                'departure_time' => '15:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 1,
                'stop_id' => 1,
                'departure_time' => '18:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Schedule 2: Warszawa - Kraków (saturday)
            [
                'schedule_id' => 2,
                'stop_id' => 1,
                'departure_time' => '08:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 2,
                'stop_id' => 1,
                'departure_time' => '12:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 2,
                'stop_id' => 1,
                'departure_time' => '16:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Schedule 3: Warszawa - Kraków (sunday)
            [
                'schedule_id' => 3,
                'stop_id' => 1,
                'departure_time' => '10:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 3,
                'stop_id' => 1,
                'departure_time' => '14:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 3,
                'stop_id' => 1,
                'departure_time' => '18:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Schedule 4: Kraków - Rzeszów (weekday)
            [
                'schedule_id' => 4,
                'stop_id' => 10,
                'departure_time' => '07:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 4,
                'stop_id' => 10,
                'departure_time' => '11:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 4,
                'stop_id' => 10,
                'departure_time' => '15:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Schedule 5: Kraków - Rzeszów (saturday)
            [
                'schedule_id' => 5,
                'stop_id' => 10,
                'departure_time' => '09:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 5,
                'stop_id' => 10,
                'departure_time' => '15:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Schedule 6: Warszawa - Lublin (weekday)
            [
                'schedule_id' => 6,
                'stop_id' => 2,
                'departure_time' => '08:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 6,
                'stop_id' => 2,
                'departure_time' => '12:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 6,
                'stop_id' => 2,
                'departure_time' => '16:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // City bus schedules
            // Schedule 7: Line 175 (weekday) - Every 15 minutes during peak hours
            [
                'schedule_id' => 7,
                'stop_id' => 1,
                'departure_time' => '06:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 7,
                'stop_id' => 1,
                'departure_time' => '06:15:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 7,
                'stop_id' => 1,
                'departure_time' => '06:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 7,
                'stop_id' => 1,
                'departure_time' => '06:45:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 7,
                'stop_id' => 1,
                'departure_time' => '07:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Schedule 8: Line 175 (saturday)
            [
                'schedule_id' => 8,
                'stop_id' => 1,
                'departure_time' => '07:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 8,
                'stop_id' => 1,
                'departure_time' => '07:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 8,
                'stop_id' => 1,
                'departure_time' => '08:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Schedule 10: Line 12 (weekday)
            [
                'schedule_id' => 10,
                'stop_id' => 9,
                'departure_time' => '07:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 10,
                'stop_id' => 9,
                'departure_time' => '07:20:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 10,
                'stop_id' => 9,
                'departure_time' => '07:40:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('departures')->insert($departures);
    }
}
