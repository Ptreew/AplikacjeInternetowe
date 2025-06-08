<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TicketsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = [
            // Reserved tickets for user 2 (Jan Kowalski)
            [
                'user_id' => 2,
                'departure_id' => 1, // Warsaw - Krakow 07:00 weekday
                'ticket_number' => 'TKT-' . Str::upper(Str::random(8)),
                'status' => 'reserved',
                'purchase_date' => Carbon::now()->subDays(1),
                'passenger_name' => 'Jan Kowalski',
                'passenger_email' => 'jan.kowalski@example.com',
                'passenger_phone' => '+48 123 456 789',
                'notes' => 'Window seat preferred',
                'is_active' => true,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            
            // Paid ticket for user 2 (Jan Kowalski)
            [
                'user_id' => 2,
                'departure_id' => 12, // Krakow - Rzeszow 07:30 weekday
                'ticket_number' => 'TKT-' . Str::upper(Str::random(8)),
                'status' => 'paid',
                'purchase_date' => Carbon::now()->subDays(5),
                'passenger_name' => 'Jan Kowalski',
                'passenger_email' => 'jan.kowalski@example.com',
                'passenger_phone' => '+48 123 456 789',
                'notes' => null,
                'is_active' => true,
                'created_at' => Carbon::now()->subDays(5)->subHours(1),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            
            // Used ticket for user 2 (Jan Kowalski) - from the past
            [
                'user_id' => 2,
                'departure_id' => 3, // Warsaw - Krakow 12:00 weekday
                'ticket_number' => 'TKT-' . Str::upper(Str::random(8)),
                'status' => 'used',
                'purchase_date' => Carbon::now()->subDays(15),
                'passenger_name' => 'Jan Kowalski',
                'passenger_email' => 'jan.kowalski@example.com',
                'passenger_phone' => '+48 123 456 789',
                'notes' => null,
                'is_active' => true,
                'created_at' => Carbon::now()->subDays(15)->subHours(2),
                'updated_at' => Carbon::now()->subDays(14),
            ],
            
            // Cancelled ticket for user 2 (Jan Kowalski)
            [
                'user_id' => 2,
                'departure_id' => 5, // Warsaw - Krakow 18:00 weekday
                'ticket_number' => 'TKT-' . Str::upper(Str::random(8)),
                'status' => 'cancelled',
                'purchase_date' => Carbon::now()->subDays(10),
                'passenger_name' => 'Jan Kowalski',
                'passenger_email' => 'jan.kowalski@example.com',
                'passenger_phone' => '+48 123 456 789',
                'notes' => 'Cancelled due to change of plans',
                'is_active' => true,
                'created_at' => Carbon::now()->subDays(10)->subHours(3),
                'updated_at' => Carbon::now()->subDays(9),
            ],
            
            // Tickets for user 3 (Anna Nowak)
            [
                'user_id' => 3,
                'departure_id' => 6, // Warsaw - Krakow 08:00 saturday
                'ticket_number' => 'TKT-' . Str::upper(Str::random(8)),
                'status' => 'paid',
                'purchase_date' => Carbon::now()->subDays(3),
                'passenger_name' => 'Anna Nowak',
                'passenger_email' => 'anna.nowak@example.com',
                'passenger_phone' => '+48 987 654 321',
                'notes' => 'Aisle seat preferred',
                'is_active' => true,
                'created_at' => Carbon::now()->subDays(3)->subHours(2),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'user_id' => 3,
                'departure_id' => 14, // Krakow - Rzeszow 15:30 weekday
                'ticket_number' => 'TKT-' . Str::upper(Str::random(8)),
                'status' => 'reserved',
                'purchase_date' => Carbon::now()->subDays(1),
                'passenger_name' => 'Anna Nowak',
                'passenger_email' => 'anna.nowak@example.com',
                'passenger_phone' => '+48 987 654 321',
                'notes' => null,
                'is_active' => true,
                'created_at' => Carbon::now()->subHours(4),
                'updated_at' => Carbon::now()->subHours(4),
            ],
            [
                'user_id' => 3,
                'departure_id' => 17, // Warszawa - Lublin 12:00 weekday
                'ticket_number' => 'TKT-' . Str::upper(Str::random(8)),
                'status' => 'used',
                'purchase_date' => Carbon::now()->subDays(20),
                'passenger_name' => 'Anna Nowak',
                'passenger_email' => 'anna.nowak@example.com',
                'passenger_phone' => '+48 987 654 321',
                'notes' => null,
                'is_active' => true,
                'created_at' => Carbon::now()->subDays(20)->subHours(3),
                'updated_at' => Carbon::now()->subDays(19),
            ],
            
            // Add tickets for multiple passengers under the same user
            [
                'user_id' => 2, // Jan Kowalski buying for family
                'departure_id' => 7, // Warsaw - Krakow 12:00 saturday
                'ticket_number' => 'TKT-' . Str::upper(Str::random(8)),
                'status' => 'paid',
                'purchase_date' => Carbon::now()->subDays(7),
                'passenger_name' => 'Maria Kowalska',
                'passenger_email' => 'maria.kowalska@example.com',
                'passenger_phone' => '+48 123 456 789',
                'notes' => 'Family ticket 1/3',
                'is_active' => true,
                'created_at' => Carbon::now()->subDays(7)->subMinutes(15),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'user_id' => 2, // Jan Kowalski buying for family
                'departure_id' => 7, // Warsaw - Krakow 12:00 saturday
                'ticket_number' => 'TKT-' . Str::upper(Str::random(8)),
                'status' => 'paid',
                'purchase_date' => Carbon::now()->subDays(7),
                'passenger_name' => 'Tomasz Kowalski',
                'passenger_email' => 'tomasz.kowalski@example.com',
                'passenger_phone' => '+48 123 456 789',
                'notes' => 'Family ticket 2/3',
                'is_active' => true,
                'created_at' => Carbon::now()->subDays(7)->subMinutes(10),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'user_id' => 2, // Jan Kowalski buying for family
                'departure_id' => 7, // Warsaw - Krakow 12:00 saturday
                'ticket_number' => 'TKT-' . Str::upper(Str::random(8)),
                'status' => 'paid',
                'purchase_date' => Carbon::now()->subDays(7),
                'passenger_name' => 'Jan Kowalski',
                'passenger_email' => 'jan.kowalski@example.com',
                'passenger_phone' => '+48 123 456 789',
                'notes' => 'Family ticket 3/3',
                'is_active' => true,
                'created_at' => Carbon::now()->subDays(7)->subMinutes(5),
                'updated_at' => Carbon::now()->subDays(7),
            ],
        ];
        
        DB::table('tickets')->insert($tickets);
    }
}
