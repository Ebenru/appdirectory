<?php

namespace App\Models;

use App\Models\Traits\HasCountry;
use App\Models\Traits\HasCommonAttributes;
use App\Models\Traits\Taggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany; // Import MorphMany
use Illuminate\Database\Eloquent\Casts\Attribute; // Import Attribute
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage; // Import Storage


class Person extends Model
{
    use HasFactory, HasCountry, HasCommonAttributes, Taggable;

    // Define categories (can be moved to a config or separate model later)
    public const CATEGORIES = [
        'tech'          => 'Technology',
        'arts'          => 'Arts',
        'business'      => 'Business',
        'science'       => 'Science',
        'media'         => 'Media & Journalism',
        'entertainment' => 'Entertainment',
        'sports'        => 'Sports',
        'politics'      => 'Politics',
        'academia'      => 'Academia',
        'healthcare'    => 'Healthcare',
        'community'     => 'Community Leaders',
        'religion'      => 'Religious Figures',
        'nonprofits'    => 'Nonprofits & NGOs',
        'influencers'   => 'Social Media Influencers',
        'activists'     => 'Activists & Advocates',
        'history'       => 'Historical Figures',
        'brands'        => 'Consumer Brands',
        'authors'       => 'Authors & Intellectuals',
        'legal'         => 'Legal Figures',
        'military'      => 'Military & Defense',
        'other'         => 'Other'
    ];

    // Use constants for icons (mapping to lucide icon names)
    public const CATEGORY_ICONS = [
        'tech'          => 'Laptop',          // Technology
        'arts'          => 'Palette',         // Arts
        'business'      => 'Briefcase',       // Business
        'science'       => 'Flask-Round',    // Science (Note: Lucide uses "FlaskRound" or "FlaskConical")
        'media'         => 'Newspaper',       // Media & Journalism
        'entertainment' => 'Clapperboard',    // Entertainment (Film/TV)
        'sports'        => 'Trophy',          // Sports
        'politics'      => 'Landmark',        // Politics (or "Scale" for justice)
        'academia'      => 'GraduationCap',   // Academia
        'healthcare'    => 'HeartPulse',      // Healthcare
        'community'     => 'Users',           // Community Leaders
        'religion'      => 'Church',          // Religious Figures (or "Cross")
        'nonprofits'    => 'HandHeart',       // Nonprofits & NGOs
        'influencers'   => 'Megaphone',       // Social Media Influencers
        'activists'     => 'Mic2',            // Activists & Advocates (or "Fist")
        'history'       => 'Scroll',          // Historical Figures
        'brands'        => 'Shopping-Bag',     // Consumer Brands
        'authors'       => 'PenLine',         // Authors & Intellectuals
        'legal'         => 'Scale',           // Legal Figures
        'military'      => 'Shield',          // Military & Defense
        'other'         => 'Wrench'           // Other/Catch-all
    ];


    protected $fillable = [
        'fullName',
        'slug',
        'country_id',
        'title',
        'picture_url',
        'photo_path',
        'description',
        'sources',
        'social_media',
        'pplCategory',
        'status',
        'rank',
        'submitted_by_id',
        'approved_by_id',
        'approved_at',
        'key_achievements',
        'featured_article_url',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'status' => 'string',
        'sources' => 'array', // Add cast for JSON field
        'social_media' => 'array', // Add cast for JSON field
    ];

    // --- Implementation for HasCommonAttributes ---
    protected function getNameAttributeForSlug(): ?string
    {
        return 'fullName'; // Use the fullName field to generate slugs
    }


    // Relationship to User who submitted
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by_id');
    }

    // Relationship to User who approved
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    // Helper to get category display name
    public function getCategoryDisplayNameAttribute(): string
    {
        return self::CATEGORIES[$this->pplCategory] ?? 'Unknown';
    }

    // Helper to get category icon name
    public function getCategoryIconNameAttribute(): string
    {
        return self::CATEGORY_ICONS[$this->pplCategory] ?? 'Briefcase'; // Default icon
    }
    /**
     * Get all the likes for this person.
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Accessor for the like count.
     * Uses $this->likes()->count() but caches the result per request.
     * Naming convention 'LikeCount' results in accessing via $person->like_count.
     */
    protected function likeCount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->likes()->count(),
        );
    }

    /**
     * Get all the comments for this person.
     * Only retrieve top-level comments by default (where parent_id is null).
     * Replies can be loaded via the comment relationship itself.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
    }

    // --- New Relationship for Nationalities ---
    /**
     * The countries representing the person's nationalities.
     */
    public function nationalities(): BelongsToMany
    {
        // Assumes a pivot table named 'country_person' (Laravel convention)
        // with 'person_id' and 'country_id' columns.
        return $this->belongsToMany(Country::class, 'country_person_pivot');
    }

    // --- Accessor for Final Image Source ---
    protected function displayPhotoUrl(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                // 1. Prioritize uploaded photo path if it exists in storage
                if (!empty($attributes['photo_path']) && Storage::disk('public')->exists($attributes['photo_path'])) {
                    return Storage::disk('public')->url($attributes['photo_path']);
                }
                // 2. Fallback to provided picture_url if it exists
                if (!empty($attributes['picture_url'])) {
                    return $attributes['picture_url'];
                }
                // 3. Fallback to default placeholder URL
                // Use the helper method if available, otherwise generate here
                // return $this->defaultProfilePhotoUrl(); // If you also added this helper method
                return 'https://ui-avatars.com/api/?name=' . urlencode($attributes['fullName'] ?? 'P') . '&color=FFFFFF&background=DC2626'; // Or generate directly
            }
        );
    }
}
