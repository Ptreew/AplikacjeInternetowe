<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        DB::table('users')->insert([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Create standard user
        DB::table('users')->insert([
            'name' => 'Jan Kowalski',
            'username' => 'jkowalski',
            'email' => 'jan.kowalski@example.com',
            'password' => Hash::make('password123'),
            'role' => 'standard',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Create additional users
        DB::table('users')->insert([
            'name' => 'Anna Nowak',
            'username' => 'anowak',
            'email' => 'anna.nowak@example.com',
            'password' => Hash::make('password123'),
            'role' => 'standard',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
