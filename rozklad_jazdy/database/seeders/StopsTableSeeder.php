<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StopsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stops = [
            // Warszawa (id=1)
            [
                'city_id' => 1,
                'name' => 'Dworzec Centralny',
                'code' => 'WA-DC',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'city_id' => 1,
                'name' => 'Dworzec Zachodni',
                'code' => 'WA-DZ',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'city_id' => 1,
                'name' => 'Lotnisko Okęcie',
                'code' => 'WA-LO',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'city_id' => 1,
                'name' => 'Rondo Dmowskiego',
                'code' => 'WA-RD',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'city_id' => 1,
                'name' => 'Aleje Jerozolimskie',
                'code' => 'WA-AJ',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'city_id' => 1,
                'name' => 'Metro Wilanowska',
                'code' => 'WA-MW',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'city_id' => 1,
                'name' => 'Natolin',
                'code' => 'WA-NA',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'city_id' => 1,
                'name' => 'Bielany',
                'code' => 'WA-BI',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Kraków (id=2)
            [
                'city_id' => 2,
                'name' => 'Dworzec Główny',
                'code' => 'KR-DG',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'city_id' => 2,
                'name' => 'MDA Kraków',
                'code' => 'KR-MDA',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'city_id' => 2,
                'name' => 'Rondo Grunwaldzkie',
                'code' => 'KR-RG',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'city_id' => 2,
                'name' => 'Nowa Huta',
                'code' => 'KR-NH',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Rzeszów (id=7)
            [
                'city_id' => 7,
                'name' => 'Dworzec Lokalny',
                'code' => 'RZ-DL',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'city_id' => 7,
                'name' => 'Rzeszów Glowny',
                'code' => 'RZ-GL',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Lublin (id=8)
            [
                'city_id' => 8,
                'name' => 'Dworzec Autobusowy',
                'code' => 'LU-DA',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tarnów (id=11)
            [
                'city_id' => 11,
                'name' => 'Dworzec PKP',
                'code' => 'TA-DK',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Radom (id=17)
            [
                'city_id' => 12,
                'name' => 'Dworzec PKP',
                'code' => 'RA-DK',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Kielce (id=18)
            [
                'city_id' => 13,
                'name' => 'Dworzec PKP',
                'code' => 'KI-DK',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('stops')->insert($stops);
    }
}
