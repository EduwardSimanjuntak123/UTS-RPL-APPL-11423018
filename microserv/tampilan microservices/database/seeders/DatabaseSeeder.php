<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * MICROSERVICES MODE: All seeding is now delegated to the MicroservicesSeeder
     * which communicates through the Go microservices APIs.
     * 
     * Laravel no longer has direct database access.
     */
    public function run(): void
    {
        // Call the microservices-based seeder
        $this->call(MicroservicesSeeder::class);
    }
}
