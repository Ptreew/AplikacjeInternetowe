<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarriersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carriers = [
            [
                'name' => 'PolBus',
                'email' => 'kontakt@polbus.example',
                'website' => 'https://www.polbus.example',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Szybki Transport',
                'email' => 'biuro@szybkitransport.example',
                'website' => 'https://www.szybkitransport.example',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Komfort Podróże',
                'email' => 'info@komfortpodroze.example',
                'website' => 'https://www.komfortpodroze.example',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Miejskie Linie Komunikacyjne',
                'email' => 'kontakt@mlk.example',
                'website' => 'https://www.mlk.example',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('carriers')->insert($carriers);
    }
}
