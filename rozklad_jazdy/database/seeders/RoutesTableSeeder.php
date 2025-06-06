<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routes = [
            // Routes for line 1 (Warszawa - Kraków)
            [
                'line_id' => 1,
                'type' => 'intercity',
                'name' => 'Warszawa - Kraków (przez Radom, Kielce)',
                'travel_time' => 210, // 3.5 godziny
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Routes for line 2 (Kraków - Rzeszów)
            [
                'line_id' => 2,
                'type' => 'intercity',
                'name' => 'Kraków - Rzeszów (przez Tarnów)',
                'travel_time' => 120, // 2 godziny
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Routes for line 3 (Warszawa - Lublin)
            [
                'line_id' => 3,
                'type' => 'intercity',
                'name' => 'Warszawa - Lublin (ekspres)',
                'travel_time' => 150, // 2.5 godziny
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Routes for city bus lines
            // Line 4 (175)
            [
                'line_id' => 4,
                'type' => 'city',
                'name' => 'Trasa podstawowa',
                'travel_time' => 45, // 45 minut
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Line 5 (12)
            [
                'line_id' => 5,
                'type' => 'city',
                'name' => 'Trasa podstawowa',
                'travel_time' => 25, // 25 minut
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Line 6 (22)
            [
                'line_id' => 6,
                'type' => 'city',
                'name' => 'Trasa podstawowa',
                'travel_time' => 30, // 30 minut
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Line 7 (501)
            [
                'line_id' => 7,
                'type' => 'city',
                'name' => 'Trasa podstawowa',
                'travel_time' => 55, // 55 minut
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Routes for PKP Intercity trains
            // Line 8 (IC 3100) Warszawa - Gdańsk
            [
                'line_id' => 8,
                'type' => 'intercity',
                'name' => 'Warszawa Centralna - Gdańsk Główny (ekspres)',
                'travel_time' => 180, // 3 godziny
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Line 9 (IC 1400) Warszawa - Poznań
            [
                'line_id' => 9,
                'type' => 'intercity',
                'name' => 'Warszawa Centralna - Poznań Główny',
                'travel_time' => 165, // 2.75 godziny
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Line 10 (IC 5400) Kraków - Wrocław
            [
                'line_id' => 10,
                'type' => 'intercity',
                'name' => 'Kraków Główny - Wrocław Główny',
                'travel_time' => 225, // 3.75 godziny
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Routes for Koleje Regionalne trains
            // Line 11 (R 10234) Warszawa - Radom
            [
                'line_id' => 11,
                'type' => 'intercity',
                'name' => 'Warszawa Zachodnia - Radom Główny',
                'travel_time' => 90, // 1.5 godziny
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Line 12 (R 12345) Kraków - Kielce
            [
                'line_id' => 12,
                'type' => 'intercity',
                'name' => 'Kraków Główny - Kielce',
                'travel_time' => 120, // 2 godziny
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('routes')->insert($routes);
    }
}
