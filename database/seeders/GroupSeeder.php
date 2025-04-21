<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;   // Import Group model
use App\Models\Country; // Import Country model
use App\Models\User;    // Import User model (for created_by)
use App\Models\Tag;     // Import Tag model
use Illuminate\Support\Str;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users and countries to associate
        $adminUser = User::where('email', 'admin@example.com')->first();
        $creator = $adminUser ?? User::first(); // Fallback to first user if admin not found
        $us = Country::where('iso_code', 'US')->first();
        $de = Country::where('iso_code', 'DE')->first();

        // Get some tags
        $techTag = Tag::where('slug', 'technology')->first();
        $consumerTag = Tag::where('slug', 'consumer-brands')->first(); // Assuming this tag exists
        $autoTag = Tag::where('slug', 'automotive')->first(); // Create if needed

        // Sample Group Data
        $groups = [
            [
                'name' => 'Alphabet Inc.',
                'description' => 'Parent company of Google, focusing on technology and internet services.',
                'industry' => 'Technology Conglomerate',
                'logo_url' => 'https://logo.clearbit.com/abc.xyz', // Alphabet's domain
                'website_url' => 'https://abc.xyz/',
                'country_id' => $us?->id,
                'status' => 'approved',
                'tags' => [$techTag?->id],
                'social_media' => ['twitter' => 'https://twitter.com/alphabetinc'],
                'sources' => ['https://en.wikipedia.org/wiki/Alphabet_Inc.'],
                'key_achievements' => "Restructuring of Google.\nInvestment in AI and autonomous vehicles." // Example with newline
            ],
            [
                'name' => 'Procter & Gamble',
                'description' => 'A multinational consumer goods corporation.',
                'industry' => 'Consumer Goods',
                'logo_url' => 'https://logo.clearbit.com/pg.com',
                'website_url' => 'https://us.pg.com/',
                'country_id' => $us?->id,
                'status' => 'approved',
                'tags' => [$consumerTag?->id],
                'social_media' => ['linkedin' => 'https://www.linkedin.com/company/procter-and-gamble/'],
                'sources' => [], // Empty example
                'key_achievements' => "Owns major brands like Pampers, Tide, Gillette.\nGlobal presence."
            ],
            [
                'name' => 'Volkswagen Group',
                'description' => 'German multinational automotive manufacturing corporation.',
                'industry' => 'Automotive',
                'logo_url' => 'https://logo.clearbit.com/volkswagenag.com',
                'website_url' => 'https://www.volkswagenag.com/en.html',
                'country_id' => $de?->id,
                'status' => 'approved',
                'tags' => [$autoTag?->id],
                'social_media' => [],
                'sources' => ['https://en.wikipedia.org/wiki/Volkswagen_Group'],
                'key_achievements' => "One of the world's largest automakers.\nOwns brands like Audi, Porsche, VW."
            ],
        ];

        foreach ($groups as $groupData) {
            // Create the group
            $group = Group::create([
                'name' => $groupData['name'],
                'slug' => Str::slug($groupData['name']), // Auto-generate slug
                'description' => $groupData['description'],
                'industry' => $groupData['industry'],
                'logo_url' => $groupData['logo_url'],
                'website_url' => $groupData['website_url'],
                'country_id' => $groupData['country_id'],
                'status' => $groupData['status'],
                'created_by' => $creator?->id, // Assign creator
                'sources' => $groupData['sources'],
                'social_media' => $groupData['social_media'],
                'key_achievements' => $groupData['key_achievements'],
                // featured_article_url is nullable
            ]);

            // Sync Tags
            $tagIdsToSync = array_filter($groupData['tags'] ?? []);
            if (!empty($tagIdsToSync)) {
                $group->tags()->sync($tagIdsToSync);
            }
        }
    }
}
