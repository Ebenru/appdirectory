<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Person;
use App\Models\User;
use App\Models\Country;
use App\Models\Tag;
use Carbon\Carbon; // Import Carbon for timestamps

class PersonSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@example.com')->first();
        $regularUser = User::where('email', 'user@example.com')->first();

        // Get some countries and tags to associate
        $us = Country::where('iso_code', 'US')->first();
        $gb = Country::where('iso_code', 'GB')->first();
        $de = Country::where('iso_code', 'DE')->first(); // Added Germany

        $techTag = Tag::where('slug', 'technology')->first();
        $scienceTag = Tag::where('slug', 'science')->first();
        $businessTag = Tag::where('slug', 'business')->first();
        $communityTag = Tag::where('slug', 'community')->first();
        $artsTag = Tag::where('slug', 'arts-culture')->first();


        $peopleData = [
            [
                'fullName' => 'Dr. Evelyn Reed',
                'title' => 'Astrophysicist',
                'pplCategory' => 'science',
                'description' => 'Pioneering research in dark matter. Joined our "Science Adherence" campaign early.',
                'status' => 'approved',
                'picture_url' => 'https://i.pravatar.cc/150?u=evelynreed', // Test URL
                'photo_path' => null, // Test no upload
                'country_id' => $gb?->id, // Use optional chaining ?.
                'tags' => [$scienceTag?->id, $techTag?->id], // Array of Tag IDs
                'social_media' => ['linkedin' => 'https://linkedin.com/in/evelynreed'],
                'sources' => ["https://example.com/research-paper", "https://news.example.org/interview-reed"]
            ],
            [
                'fullName' => 'Marcus Chen',
                'title' => 'CEO & Founder',
                'pplCategory' => 'business',
                'description' => 'Championed sustainable tech adoption via our "Green Business" initiative.',
                'status' => 'approved',
                'picture_url' => null, // Test no URL
                'photo_path' => 'person-photos/placeholder1.jpg', // Test path (ensure file exists or remove for testing)
                'country_id' => $us?->id,
                'tags' => [$businessTag?->id, Tag::where('slug', 'sustainability')->first()?->id],
                'social_media' => ['twitter' => 'https://twitter.com/marcuschen', 'linkedin' => 'https://linkedin.com/in/marcuschen'],
                'sources' => ["https://company-site.example/about"]
            ],
            // ... (Add similar comprehensive data for Anya Petrova, Leo Maxwell, Samira Khan) ...
            [
                'fullName' => 'Leo Maxwell',
                'title' => 'Digital Artist',
                'pplCategory' => 'arts',
                'description' => 'Created stunning visuals celebrating campaign milestones.',
                'status' => 'pending', // Test Pending
                'picture_url' => 'https://i.pravatar.cc/150?u=leomaxwell',
                'photo_path' => null,
                'country_id' => $de?->id, // Test different country
                'tags' => [$artsTag?->id],
                'social_media' => ['instagram' => 'https://instagram.com/leomaxwellart'],
                'sources' => [] // Test empty sources
            ],
        ];

        foreach ($peopleData as $data) {
            // Create the person first
            $person = Person::create([
                'fullName' => $data['fullName'],
                'title' => $data['title'],
                'pplCategory' => $data['pplCategory'],
                'country_id' => $data['country_id'], // Add country
                'description' => $data['description'],
                'picture_url' => $data['picture_url'],
                'photo_path' => $data['photo_path'], // Add photo path
                'sources' => $data['sources'], // Add sources
                'social_media' => $data['social_media'], // Add social media
                'status' => $data['status'],
                'rank' => 0,
                'submitted_by_id' => $regularUser->id,
                'approved_by_id' => $data['status'] == 'approved' ? $adminUser->id : null,
                'approved_at' => $data['status'] == 'approved' ? Carbon::now() : null,
                // Slug auto-generated
            ]);

            // Sync Tags after creation
            $tagIdsToSync = array_filter($data['tags'] ?? []); // Filter out null IDs if tags weren't found
            if (!empty($tagIdsToSync)) {
                $person->tags()->sync($tagIdsToSync);
            }
        }
    }
}
