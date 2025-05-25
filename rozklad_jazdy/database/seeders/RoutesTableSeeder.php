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
                'name' => 'Warszawa - Kraków (przez Radom, Kielce)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Routes for line 2 (Kraków - Rzeszów)
            [
                'line_id' => 2,
                'name' => 'Kraków - Rzeszów (przez Tarnów)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Routes for line 3 (Warszawa - Lublin)
            [
                'line_id' => 3,
                'name' => 'Warszawa - Lublin (ekspres)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Routes for city bus lines
            // Line 4 (175)
            [
                'line_id' => 4,
                'name' => 'Trasa podstawowa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Line 5 (12)
            [
                'line_id' => 5,
                'name' => 'Trasa podstawowa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Line 6 (22)
            [
                'line_id' => 6,
                'name' => 'Trasa podstawowa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Line 7 (501)
            [
                'line_id' => 7,
                'name' => 'Trasa podstawowa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('routes')->insert($routes);
    }
}
