<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event; // Import Event
use App\Models\Country;
use App\Models\User;
use App\Models\Tag;
use Carbon\Carbon; // Import Carbon
use Illuminate\Support\Str;


class EventSeeder extends Seeder
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

        $activismTag = Tag::where('slug', 'activism')->first();
        $envTag = Tag::where('slug', 'environment')->first();
        $hrTag = Tag::where('slug', 'human-rights')->first();


        $events = [
            [
                'name' => 'Global Climate Strike Summit',
                'slug' => Str::slug('Global Climate Strike Summit 2024'), // Make unique
                'description' => 'A major gathering of activists and organizations demanding urgent climate action.',
                'startDate' => Carbon::now()->addMonths(2)->setHour(9),
                'endDate' => Carbon::now()->addMonths(2)->addDays(2)->setHour(17),
                'location' => 'Online & New York City, NY',
                'is_virtual' => true,
                'eventType' => 'Awareness Campaign',
                // 'featured_image_url' => 'https://via.placeholder.com/400x200.png/00aa44?text=Climate+Summit',
                'website_url' => 'https://example.com/climate-summit',
                'country_id' => $us?->id,
                'status' => 'approved',
                'created_by' => $creator?->id,
                'tags' => [$activismTag?->id, $envTag?->id],
                'social_media' => ['twitter' => 'https://twitter.com/hashtag/ClimateSummit'],
                'key_achievements' => "Mobilized millions globally.\nInfluenced policy discussions."
            ],
            [
                'name' => 'Human Rights Watch Film Festival',
                'slug' => Str::slug('Human Rights Watch Film Festival London 2025'),
                'description' => 'Showcasing courageous storytelling about human rights issues from around the world.',
                'startDate' => Carbon::now()->addMonths(4)->setHour(18),
                'endDate' => Carbon::now()->addMonths(4)->addDays(10)->setHour(22),
                'location' => 'Various venues, London',
                'is_virtual' => false,
                'eventType' => 'Arts & Culture Event',
                // 'featured_image_url' => 'https://via.placeholder.com/400x200.png/cc0000?text=HRW+Film+Fest',
                'website_url' => 'https://ff.hrw.org/',
                'country_id' => $gb?->id,
                'status' => 'approved',
                'created_by' => $creator?->id,
                'tags' => [$hrTag?->id, Tag::where('slug', 'arts-culture')->first()?->id],
                'social_media' => [],
                'key_achievements' => "Raises awareness through film.\nProvides platform for filmmakers."
            ],
        ];

        foreach ($events as $eventData) {
            $event = Event::create([
                'name' => $eventData['name'],
                'slug' => $eventData['slug'],
                'description' => $eventData['description'],
                'startDate' => $eventData['startDate'],
                'endDate' => $eventData['endDate'],
                'location' => $eventData['location'],
                'is_virtual' => $eventData['is_virtual'],
                'eventType' => $eventData['eventType'],
                // 'featured_image_path' => null,
                'featured_image_url' => $eventData['featured_image_url'] ?? null,
                'website_url' => $eventData['website_url'],
                'country_id' => $eventData['country_id'],
                'status' => $eventData['status'],
                'created_by' => $eventData['created_by'],
                'sources' => $eventData['sources'] ?? [],
                'social_media' => $eventData['social_media'] ?? [],
                'key_achievements' => $eventData['key_achievements'],
                // 'featured_article_url' => null,
            ]);

            // Sync Tags
            $tagIdsToSync = array_filter($eventData['tags'] ?? []);
            if (!empty($tagIdsToSync)) {
                $event->tags()->sync($tagIdsToSync);
            }
        }
    }
}
