<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LinesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lines = [
            // Intercity bus lines (PolBus, id=1)
            [
                'carrier_id' => 1,
                'number' => null,
                'name' => 'Warszawa - Kraków',
                'color' => '#4a8f29', // PolBus green
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'carrier_id' => 1,
                'number' => null,
                'name' => 'Kraków - Rzeszów',
                'color' => '#4a8f29',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Szybki Transport (id=2)
            [
                'carrier_id' => 2,
                'number' => null,
                'name' => 'Warszawa - Lublin',
                'color' => '#0077cc',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // City buses (Miejskie Linie Komunikacyjne, id=4)
            [
                'carrier_id' => 4,
                'number' => 175,
                'name' => 'Dworzec Północny - Port Lotniczy',
                'color' => '#e30613',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'carrier_id' => 4,
                'number' => 12,
                'name' => 'Dworzec Główny - Os. Słoneczne',
                'color' => '#0033a0',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'carrier_id' => 4,
                'number' => 22,
                'name' => 'Aleja Wolności - Os. Zielone',
                'color' => '#e30613',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'carrier_id' => 4,
                'number' => 501,
                'name' => 'Centrum Handlowe - Os. Wschód',
                'color' => '#ffdd00',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('lines')->insert($lines);
    }
}
