<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag; // Import Tag
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Technology',
            'Healthcare',
            'Environment',
            'Sustainability',
            'Education',
            'Social Impact',
            'Activism',
            'Human Rights',
            'Finance',
            'Manufacturing',
            'Retail',
            'Arts & Culture',
            'Community',
            'Research',
            'Open Source'
        ];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => $tagName]
            );
        }
    }
}
