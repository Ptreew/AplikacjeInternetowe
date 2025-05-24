<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // The order is important to maintain proper relationships
        
        // First, seed the users table
        $this->call(UsersTableSeeder::class);
        
        // Then seed the carriers and cities (base entities)
        $this->call(CarriersTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
        
        // Then seed the lines and stops (depend on carriers and cities)
        $this->call(LinesTableSeeder::class);
        $this->call(StopsTableSeeder::class);
        
        // Then seed the routes (depend on lines)
        $this->call(RoutesTableSeeder::class);
        
        // Then seed the route_stops (junction table, depends on routes and stops)
        $this->call(RouteStopsTableSeeder::class);
        
        // Then seed the schedules (depend on routes)
        $this->call(SchedulesTableSeeder::class);
        
        // Then seed the departures (depend on schedules and stops)
        $this->call(DeparturesTableSeeder::class);
        
        // Finally, seed the vehicles (depend on lines)
        $this->call(VehiclesTableSeeder::class);
        
        // favourite_lines will be populated through user interaction
    }
}
