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
            // Route 1: Warszawa - Kraków - wszystkie dni tygodnia
            [
                'route_id' => 1,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'days_of_week' => json_encode([1, 2, 3, 4, 5]), // Dni robocze (Pon-Pt)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 1,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'days_of_week' => json_encode([0, 6]), // Weekendy (Nd, Sb)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Route 2: Kraków - Rzeszów - dni robocze i soboty
            [
                'route_id' => 2,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'days_of_week' => json_encode([1, 2, 3, 4, 5]), // Dni robocze (Pon-Pt)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 2,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'days_of_week' => json_encode([6]), // Sobota
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Route 3: Rzeszów - Lublin - tylko dni robocze
            [
                'route_id' => 3,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'days_of_week' => json_encode([1, 2, 3, 4, 5]), // Dni robocze (Pon-Pt)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Route 4: Lublin - Warszawa - wszystkie dni
            [
                'route_id' => 4,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'days_of_week' => json_encode([0, 1, 2, 3, 4, 5, 6]), // Wszystkie dni (Nd-Sb)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Route 5: Warszawa - Gdańsk - dni robocze i soboty
            [
                'route_id' => 5,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'days_of_week' => json_encode([1, 2, 3, 4, 5]), // Dni robocze (Pon-Pt)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 5,
                'valid_from' => "$year-01-01",
                'valid_to' => "$year-12-31",
                'days_of_week' => json_encode([6]), // Sobota
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('schedules')->insert($schedules);
    }
}
