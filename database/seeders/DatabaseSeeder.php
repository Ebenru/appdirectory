<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CountrySeeder::class, // <-- RUN COUNTRIES FIRST
            UserSeeder::class,    // Then users
            TagSeeder::class,     // <-- Seed tags if needed (Create this seeder next)
            PersonSeeder::class,  // Then people
            CompanySeeder::class, // Then companies
            GroupSeeder::class,   // Then groups
            OrganizationSeeder::class, // Then organizations
            EventSeeder::class, // Then events
            // Add other seeders here
        ]);
    }
}
