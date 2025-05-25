<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RouteStopsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routeStops = [
            // Route 1: Warszawa - Kraków (przez Radom, Kielce) - Assuming some stops for Radom and Kielce
            [
                'route_id' => 1,
                'stop_id' => 1, // Warszawa Dworzec Centralny
                'stop_number' => 1,
                'distance_from_start' => 0,
                'time_to_next' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 1,
                'stop_id' => 9, // Kraków Dworzec Główny
                'stop_number' => 2, 
                'distance_from_start' => 292000, // 292 km
                'time_to_next' => 0, // Last stop
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Route 2: Kraków - Rzeszów (przez Tarnów) - Assuming Tarnów would be in the middle
            [
                'route_id' => 2,
                'stop_id' => 10, // Kraków MDA
                'stop_number' => 1,
                'distance_from_start' => 0,
                'time_to_next' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 2,
                'stop_id' => 13, // Rzeszów Dworzec Lokalny
                'stop_number' => 2,
                'distance_from_start' => 160000, // 160 km
                'time_to_next' => 0, // Last stop
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Route 3: Warszawa - Lublin (ekspres)
            [
                'route_id' => 3,
                'stop_id' => 2, // Warszawa Dworzec Zachodni
                'stop_number' => 1,
                'distance_from_start' => 0,
                'time_to_next' => 120,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 3,
                'stop_id' => 15, // Lublin Dworzec Autobusowy
                'stop_number' => 2,
                'distance_from_start' => 170000, // 170 km
                'time_to_next' => 0, // Last stop
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Route 4: City bus line 175 (Dworzec Centralny - Lotnisko Okęcie)
            [
                'route_id' => 4,
                'stop_id' => 1, // Warszawa Dworzec Centralny
                'stop_number' => 1,
                'distance_from_start' => 0,
                'time_to_next' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 4,
                'stop_id' => 3, // Warszawa Lotnisko Okęcie
                'stop_number' => 2,
                'distance_from_start' => 7200, // 7.2 km
                'time_to_next' => 0, // Last stop
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Route 5: City bus line 12 (Dworzec Główny - Nowa Huta)
            [
                'route_id' => 5,
                'stop_id' => 9, // Kraków Dworzec Główny
                'stop_number' => 1,
                'distance_from_start' => 0,
                'time_to_next' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 5,
                'stop_id' => 11, // Kraków Rondo Grunwaldzkie
                'stop_number' => 2,
                'distance_from_start' => 3000, // 3 km
                'time_to_next' => 18,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 5,
                'stop_id' => 12, // Kraków Nowa Huta
                'stop_number' => 3,
                'distance_from_start' => 12000, // 12 km
                'time_to_next' => 0, // Last stop
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Route 6: City bus line 22 (Aleje Jerozolimskie - Bielany)
            [
                'route_id' => 6,
                'stop_id' => 5, // Warszawa Aleje Jerozolimskie
                'stop_number' => 1,
                'distance_from_start' => 0,
                'time_to_next' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 6,
                'stop_id' => 8, // Warszawa Bielany
                'stop_number' => 2,
                'distance_from_start' => 8500, // 8.5 km
                'time_to_next' => 0, // Last stop
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Route 7: City bus line 501 (Metro Wilanowska - Natolin)
            [
                'route_id' => 7,
                'stop_id' => 6, // Warszawa Metro Wilanowska
                'stop_number' => 1,
                'distance_from_start' => 0,
                'time_to_next' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'route_id' => 7,
                'stop_id' => 7, // Warszawa Natolin
                'stop_number' => 2,
                'distance_from_start' => 5000, // 5 km
                'time_to_next' => 0, // Last stop
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('route_stops')->insert($routeStops);
    }
}
