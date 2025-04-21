<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country; // Import Country model
use Illuminate\Support\Str; // Import Str for slug generation

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'United States', 'iso_code' => 'US', 'region' => 'North America', 'flag_icon_url' => 'https://www.theflagshop.co.uk/media/catalog/product/cache/c2fa30bfcfb937c19a4b2b38aef6c453/u/s/usa-united-states-of-america-flag-3ft-x-2ft-249-p_1.jpg'], // Add flag URLs later if desired
            ['name' => 'Canada', 'iso_code' => 'CA', 'region' => 'North America', 'flag_icon_url' => null],
            ['name' => 'United Kingdom', 'iso_code' => 'GB', 'region' => 'Europe', 'flag_icon_url' => null],
            ['name' => 'Germany', 'iso_code' => 'DE', 'region' => 'Europe', 'flag_icon_url' => null],
            ['name' => 'Japan', 'iso_code' => 'JP', 'region' => 'Asia', 'flag_icon_url' => null],
            ['name' => 'Australia', 'iso_code' => 'AU', 'region' => 'Oceania', 'flag_icon_url' => null], // Added one more for variety
        ];

        foreach ($countries as $countryData) {
            Country::firstOrCreate(
                ['iso_code' => $countryData['iso_code']], // Find/create by unique ISO code
                [
                    'name' => $countryData['name'],
                    'slug' => Str::slug($countryData['name']), // Auto-generate slug
                    'region' => $countryData['region'],
                    'flag_icon_url' => $countryData['flag_icon_url'],
                ]
            );
        }
    }
}
