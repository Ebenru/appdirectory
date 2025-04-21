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
use Illuminate\Support\Facades\Storage; // Import Storage


class Company extends Model
{
    // Use traits for common functionality
    use HasFactory, HasCountry, HasCommonAttributes, Taggable;

    // Define categories
    public const CATEGORIES = [
        'saas'         => 'SaaS',
        'retail'       => 'Retail',
        'manu'         => 'Manufacturing',
        'finance'      => 'Finance',
        'healthcare'   => 'Healthcare',
        'education'    => 'Education',
        'tech'         => 'Technology',
        'energy'       => 'Energy',
        'transport'    => 'Transportation',
        'hospitality'  => 'Hospitality',
        'media'        => 'Media & Entertainment',
        'nonprofit'    => 'Non-Profit',
        'government'   => 'Government',
        'construction' => 'Construction',
        'agriculture'  => 'Agriculture',
        'other'        => 'Other',
    ];

    // Use constants for icons
    public const CATEGORY_ICONS = [
        'saas'         => 'Cloud',           // SaaS (Cloud services)
        'retail'       => 'Shopping-Bag',     // Retail (or "Store")
        'manu'         => 'Factory',         // Manufacturing
        'finance'      => 'Landmark',        // Finance (banking/institutions)
        'healthcare'   => 'HeartPulse',     // Healthcare
        'education'    => 'GraduationCap',  // Education
        'tech'         => 'Cpu',            // Technology (or "CircuitBoard")
        'energy'       => 'BatteryFull',    // Energy (or "Zap")
        'transport'    => 'Truck',          // Transportation
        'hospitality'  => 'Hotel',          // Hospitality (or "Utensils" for food)
        'media'        => 'Film',           // Media & Entertainment
        'nonprofit'    => 'Hand-Heart',      // Non-Profit (or "Heart")
        'government'   => 'Scale',          // Government (or "Flag")
        'construction' => 'Hammer',         // Construction
        'agriculture'  => 'Wheat',          // Agriculture (or "Tree")
        'other'        => 'Wrench',         // Other/Catch-all
    ];


    protected $fillable = [
        'legalName',
        'displayName',
        'slug', // Add slug
        'country_id', // Add country_id
        'group_id', // Add group_id
        'logo_url',
        'logo_path',
        'description',
        'sources',
        'social_media', // Add social_media
        'cmpCategory',
        'website_url',
        'status',
        'rank',
        'submitted_by_id',
        'approved_by_id',
        'approved_at',
        'key_achievements',
        'featured_article_url',
        // Add group_id later when Group model exists
        // 'group_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'status' => 'string',
        'sources' => 'array',
        'social_media' => 'array',
    ];

    // --- Implementation for HasCommonAttributes ---
    protected function getNameAttributeForSlug(): ?string
    {
        // Use legalName as the basis for the slug
        return 'legalName';
    }

    // Relationships
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    // Helper to get category display name
    public function getCategoryDisplayNameAttribute(): string
    {
        return self::CATEGORIES[$this->cmpCategory] ?? 'Unknown';
    }

    // Helper to get category icon name
    public function getCategoryIconNameAttribute(): string
    {
        return self::CATEGORY_ICONS[$this->cmpCategory] ?? 'Briefcase'; // Default icon
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Accessor for the like count.
     */
    protected function likeCount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->likes()->count(),
        );
    }
    /**
     * Get all the comments for this company.
     * Only retrieve top-level comments by default.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
    }

    protected function displayLogoUrl(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                // 1. Prioritize uploaded logo path if it exists in storage
                if (!empty($attributes['logo_path']) && Storage::disk('public')->exists($attributes['logo_path'])) {
                    return Storage::disk('public')->url($attributes['logo_path']);
                }
                // 2. Fallback to provided logo_url if it exists
                if (!empty($attributes['logo_url'])) {
                    return $attributes['logo_url'];
                }
                // 3. Fallback to default placeholder image asset
                // Make sure 'public/images/default-logo-placeholder.png' exists
                return asset('images/default-logo-placeholder.png');
            }
        );
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
