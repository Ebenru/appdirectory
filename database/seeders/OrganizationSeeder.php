<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organization; // Import Organization
use App\Models\Country;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@example.com')->first();
        $creator = $adminUser ?? User::first();
        $us = Country::where('iso_code', 'US')->first();
        $gb = Country::where('iso_code', 'GB')->first();
        $ch = Country::where('name', 'Switzerland')->first(); // Assuming CH exists or add it

        $nonprofitTag = Tag::where('slug', 'nonprofits-ngos')->first();
        $hrTag = Tag::where('slug', 'human-rights')->first();
        $envTag = Tag::where('slug', 'environment')->first();
        $sportsTag = Tag::where('slug', 'sports')->first();

        $organizations = [
            [
                'name' => 'Amnesty International',
                'slug' => Str::slug('Amnesty International'),
                'description' => 'A global movement campaigning for a world where human rights are enjoyed by all.',
                'type' => 'NGO',
                'foundingYear' => 1961,
                'scope' => 'International',
                'logo_url' => 'https://logo.clearbit.com/amnesty.org',
                'website_url' => 'https://www.amnesty.org/',
                'country_id' => $gb?->id, // HQ Location
                'status' => 'approved',
                'created_by' => $creator?->id,
                'tags' => [$hrTag?->id, $nonprofitTag?->id],
                'social_media' => ['twitter' => 'https://twitter.com/amnesty'],
                'sources' => ['https://en.wikipedia.org/wiki/Amnesty_International'],
                'key_achievements' => "Nobel Peace Prize winner.\nCampaigns against torture and the death penalty."
            ],
            [
                'name' => 'Greenpeace',
                'slug' => Str::slug('Greenpeace'),
                'description' => 'An independent global campaigning network that acts to change attitudes and behavior, to protect and conserve the environment and to promote peace.',
                'type' => 'NGO',
                'foundingYear' => 1971,
                'scope' => 'International',
                'logo_url' => 'https://logo.clearbit.com/greenpeace.org',
                'website_url' => 'https://www.greenpeace.org/',
                'country_id' => $us?->id, // Founded in Canada, HQ in Netherlands - adjust country as needed
                'status' => 'approved',
                'created_by' => $creator?->id,
                'tags' => [$envTag?->id, $nonprofitTag?->id],
                'social_media' => ['twitter' => 'https://twitter.com/Greenpeace'],
                'sources' => ['https://en.wikipedia.org/wiki/Greenpeace'],
                'key_achievements' => "Direct action campaigns.\nAdvocacy for renewable energy."
            ],
            [
                'name' => 'FIFA (Fédération Internationale de Football Association)',
                'slug' => Str::slug('FIFA'),
                'description' => 'International governing body of association football, futsal and beach football.',
                'type' => 'International Org', // Or Governing Body
                'foundingYear' => 1904,
                'scope' => 'International',
                'logo_url' => 'https://logo.clearbit.com/fifa.com',
                'website_url' => 'https://www.fifa.com/',
                'country_id' => $ch?->id, // HQ in Switzerland
                'status' => 'approved',
                'created_by' => $creator?->id,
                'tags' => [$sportsTag?->id],
                'social_media' => ['twitter' => 'https://twitter.com/FIFAcom'],
                'sources' => ['https://en.wikipedia.org/wiki/FIFA'],
                'key_achievements' => "Organizes the FIFA World Cup.\nGoverns international football competitions."
            ],
        ];

        foreach ($organizations as $orgData) {
            $organization = Organization::create([
                'name' => $orgData['name'],
                'slug' => $orgData['slug'],
                'description' => $orgData['description'],
                'type' => $orgData['type'],
                'foundingYear' => $orgData['foundingYear'],
                'scope' => $orgData['scope'],
                'logo_url' => $orgData['logo_url'],
                // 'logo_path' => null, // Set if uploading default image
                'website_url' => $orgData['website_url'],
                'country_id' => $orgData['country_id'],
                'status' => $orgData['status'],
                'created_by' => $orgData['created_by'],
                'sources' => $orgData['sources'] ?? [],
                'social_media' => $orgData['social_media'] ?? [],
                'key_achievements' => $orgData['key_achievements'],
                // 'featured_article_url' => null,
            ]);

            // Sync Tags
            $tagIdsToSync = array_filter($orgData['tags'] ?? []);
            if (!empty($tagIdsToSync)) {
                $organization->tags()->sync($tagIdsToSync);
            }
        }
    }
}
