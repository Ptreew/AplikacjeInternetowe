<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Current year
        $year = date('Y');
        
        $schedules = [
            // Intercity routes - all year schedules
            // Route 1: Warszawa - Kraków
            [
                'route_id' => 1,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'day_type' => 'weekday',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 1,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'day_type' => 'saturday',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 1,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'day_type' => 'sunday',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Route 2: Kraków - Rzeszów
            [
                'route_id' => 2,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'day_type' => 'weekday',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 2,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'day_type' => 'saturday',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Route 3: Warszawa - Lublin
            [
                'route_id' => 3,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'day_type' => 'weekday',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // City buses - separate schedules for weekdays and weekends
            // Route 4: City bus line 175 (Dworzec Centralny - Lotnisko Okęcie)
            [
                'route_id' => 4,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'day_type' => 'weekday',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 4,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'day_type' => 'saturday',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 4,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'day_type' => 'sunday',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Route 5: City bus line 12 (Dworzec Główny - Nowa Huta)
            [
                'route_id' => 5,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'day_type' => 'weekday',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 5,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'day_type' => 'saturday',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('schedules')->insert($schedules);
    }
}
