<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            [
                'name' => 'Warszawa',
                'voivodeship' => 'mazowieckie',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kraków',
                'voivodeship' => 'małopolskie',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Łódź',
                'voivodeship' => 'łódzkie',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Wrocław',
                'voivodeship' => 'dolnośląskie',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Poznań',
                'voivodeship' => 'wielkopolskie',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gdańsk',
                'voivodeship' => 'pomorskie',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rzeszów',
                'voivodeship' => 'podkarpackie',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lublin',
                'voivodeship' => 'lubelskie',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Katowice',
                'voivodeship' => 'śląskie',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Białystok',
                'voivodeship' => 'podlaskie',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('cities')->insert($cities);
    }
}
