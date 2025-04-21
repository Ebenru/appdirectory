<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Country;
use App\Models\Tag;
use Carbon\Carbon; // Import Carbon

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@example.com')->first();
        $regularUser = User::where('email', 'user@example.com')->first();

        $us = Country::where('iso_code', 'US')->first();
        $gb = Country::where('iso_code', 'GB')->first();

        $saasTag = Tag::where('slug', 'saas')->first(); // Assuming SaaS is also a tag
        $retailTag = Tag::where('slug', 'retail')->first();
        $financeTag = Tag::where('slug', 'finance')->first();
        $nonprofitTag = Tag::where('slug', 'non-profit')->first(); // Assuming slug match

        $companiesData = [
            [
                'legalName' => 'QuantumLeap Solutions Inc.',
                'displayName' => 'QuantumLeap',
                'cmpCategory' => 'saas',
                'description' => 'Provided pro-bono access to their analytics platform.',
                'status' => 'approved',
                'logo_url' => null, // Test upload
                'logo_path' => 'company-logos/quantumleap.png', // Test path (ensure file exists or set null)
                'website_url' => 'https://example.com/quantumleap',
                'country_id' => $us?->id,
                'tags' => [$saasTag?->id, Tag::where('slug', 'technology')->first()?->id],
                'social_media' => ['linkedin' => 'https://linkedin.com/company/quantumleap'],
                'sources' => ["https://news.example.com/quantumleap-partnership"]
            ],
            [
                'legalName' => 'Evergreen Goods Ltd.',
                'displayName' => 'Evergreen',
                'cmpCategory' => 'retail',
                'description' => 'Sponsored our community fair.',
                'status' => 'approved',
                'logo_url' => 'https://logo.clearbit.com/evergreen.com', // Test URL
                'logo_path' => null,
                'website_url' => 'https://example.com/evergreen',
                'country_id' => $gb?->id,
                'tags' => [$retailTag?->id, Tag::where('slug', 'sustainability')->first()?->id],
                'social_media' => [], // Test empty social
                'sources' => ["https://communityfair.example/sponsors"]
            ],
            // ... Add comprehensive data for Apex, Starlight, Hope Foundation ...
            [
                'legalName' => 'Hope Foundation',
                'displayName' => 'Hope Foundation',
                'cmpCategory' => 'nonprofit',
                'description' => 'Partnered on outreach programs, significantly boosting campaign adherence.',
                'status' => 'approved',
                'logo_url' => 'https://logo.clearbit.com/hope.org',
                'logo_path' => null,
                'website_url' => 'https://example.com/hope',
                'country_id' => $us?->id,
                'tags' => [$nonprofitTag?->id, Tag::where('slug', 'community')->first()?->id],
                'social_media' => ['twitter' => 'https://twitter.com/hopefoundation'],
                'sources' => ["https://hope.example.org/our-work"]
            ],

        ];

        foreach ($companiesData as $data) {
            // Create company
            $company = Company::create([
                'legalName' => $data['legalName'],
                'displayName' => $data['displayName'],
                'cmpCategory' => $data['cmpCategory'],
                'country_id' => $data['country_id'], // Add country
                'description' => $data['description'],
                'website_url' => $data['website_url'],
                'logo_url' => $data['logo_url'],
                'logo_path' => $data['logo_path'], // Add path
                'sources' => $data['sources'], // Add sources
                'social_media' => $data['social_media'], // Add social
                'status' => $data['status'],
                'rank' => 0,
                'submitted_by_id' => $regularUser->id,
                'approved_by_id' => $data['status'] == 'approved' ? $adminUser->id : null,
                'approved_at' => $data['status'] == 'approved' ? Carbon::now() : null,
            ]);
            // Sync Tags
            $tagIdsToSync = array_filter($data['tags'] ?? []);
            if (!empty($tagIdsToSync)) {
                $company->tags()->sync($tagIdsToSync);
            }
        }
    }
}
