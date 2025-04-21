<?php

namespace App\Models;

use App\Models\Traits\HasCountry;
use App\Models\Traits\HasCommonAttributes;
use App\Models\Traits\Taggable;
use Illuminate\Database\Eloquent\Casts\Attribute; // Keep for potential accessors
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // For country, created_by
use Illuminate\Database\Eloquent\Relations\HasMany;   // For companies directly linked
use Illuminate\Database\Eloquent\Relations\MorphMany; // For likes, comments
use Illuminate\Database\Eloquent\Relations\MorphToMany; // For taggables
use Illuminate\Support\Facades\Storage; // For logo accessor

class Group extends Model
{
    // Use relevant traits
    use HasFactory, HasCountry, HasCommonAttributes, Taggable;
    // Add IsLikeable, IsCommentable etc. traits if you create them later

    protected $fillable = [
        'name', // From CommonAttributes
        'slug', // From CommonAttributes
        'description', // From CommonAttributes
        'industry', // Specific
        'logo_path', // Common-like
        'logo_url', // Common-like
        'website_url', // From CommonAttributes
        'country_id', // From HasCountry
        'status', // Common 
        'created_by', // Common 
        'sources', // From CommonAttributes
        'social_media', // From CommonAttributes
        'key_achievements', // From CommonAttributes 
        'featured_article_url', // From CommonAttributes 
        'group_id', // From CommonAttributes 
    ];

    protected $casts = [
        'sources' => 'array',
        'social_media' => 'array',
        'status' => 'string', // Keep cast if needed
    ];

    // --- Implementation for HasCommonAttributes ---
    protected function getNameAttributeForSlug(): ?string
    {
        return 'name'; // Use 'name' which replaced 'groupName'
    }
    // --- End Implementation ---


    // --- Relationships ---

    // Companies directly belonging to this group (via group_id FK on companies)
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    // Polymorphic "members" if Groups can contain more than just Companies
    // public function members(): MorphToMany
    // {
    //     return $this->morphToMany(Model::class, 'groupable'); // Define specific models if possible
    // }

    // Shared features via Traits/BaseModel or defined explicitly:
    // Example: Define explicitly if not using traits yet for these
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
    }
    // public function reports(): MorphMany { ... }
    // public function campaignCall(): MorphOne { ... }

    // Accessor for logo (combines path and url) - Copy/Adapt from Company model
    protected function displayLogoUrl(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if (!empty($attributes['logo_path']) && Storage::disk('public')->exists($attributes['logo_path'])) {
                    return Storage::disk('public')->url($attributes['logo_path']);
                }
                if (!empty($attributes['logo_url'])) {
                    return $attributes['logo_url'];
                }
                return asset('images/default-logo-placeholder.png'); // Default
            }
        );
    }

    // Accessor for like count (Copy/Adapt from Person/Company)
    protected function likeCount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->likes()->count(),
        );
    }

    // --- End Relationships & Accessors ---
}
