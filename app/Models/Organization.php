<?php

namespace App\Models;

// --- Base Imports ---
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// --- Trait Imports ---
use App\Models\Traits\HasCountry;
use App\Models\Traits\HasCommonAttributes;
use App\Models\Traits\Taggable;

// --- Relationship Imports ---
use Illuminate\Database\Eloquent\Relations\BelongsTo; // For country, created_by
use Illuminate\Database\Eloquent\Relations\MorphMany; // For likes, comments, tags(via Trait), reports, campaign calls
use Illuminate\Database\Eloquent\Relations\MorphToMany; // For event sponsoring, group membership (if polymorphic)
// use Illuminate\Database\Eloquent\Relations\BelongsToMany; // For direct memberships if needed

// --- Other Imports ---
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;


class Organization extends Model
{
    // --- Use Traits ---
    // Includes HasFactory by default
    // HasCountry adds ->country() relationship & scope
    // HasCommonAttributes adds ->sources, ->socialMedia accessors/mutators, slug generation, getRouteKeyName()
    // Taggable adds ->tags() relationship & scopes
    use HasFactory, HasCountry, HasCommonAttributes, Taggable;

    // --- Fillable Properties ---
    // Define ALL attributes that can be mass-assigned via create() or update()
    protected $fillable = [
        'name', // Common
        'slug', // Common
        'description', // Common
        'type', // Specific
        'foundingYear', // Specific
        'scope', // Specific
        'logo_path', // Common-like (path for uploaded logo)
        'logo_url', // Common-like (URL for external logo)
        'website_url', // Common
        'country_id', // Common (FK from HasCountry)
        'status', // Common
        'created_by', // Common (FK, assumed to be handled by trait/manually)
        'sources', // Common (JSON)
        'social_media', // Common (JSON)
        'key_achievements', // Common
        'featured_article_url', // Common
    ];

    // --- Attribute Casting ---
    // Define how attributes should be cast when accessed/saved
    protected $casts = [
        'sources' => 'array',       // Auto encode/decode JSON
        'social_media' => 'array',  // Auto encode/decode JSON
        'status' => 'string',       // Treat status enum as string
        'foundingYear' => 'integer', // Cast year to integer
        // Dates are automatically cast by Laravel unless specified otherwise
        // 'created_at' => 'datetime',
        // 'updated_at' => 'datetime',
    ];

    // --- Implementation for HasCommonAttributes ---
    // Required by the trait to know which field to use for slug generation
    protected function getNameAttributeForSlug(): ?string
    {
        return 'name'; // Use the 'name' field
    }
    // --- End Implementation ---


    // --- Relationships ---

    // Relationships added by Traits:
    // country() from HasCountry
    // tags() from Taggable

    // Common Polymorphic Relationships (defined explicitly or via Traits if created)
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
    }
    // Define others later: reports(), campaignCall(), etc.

    // Relationship to User who created/submitted this entry
    public function creator(): BelongsTo // Renamed from submittedBy for clarity if needed
    {
        return $this->belongsTo(User::class, 'created_by'); // Assuming 'created_by' FK column
    }

    // TODO: Define Specific Relationships for Organization
    // Example: Events sponsored by this organization (Polymorphic Many-to-Many)
    // public function sponsoredEvents(): MorphToMany
    // {
    //     return $this->morphedByMany(Event::class, 'sponsorable', 'event_sponsors'); // Assumes pivot table 'event_sponsors'
    // }

    // Example: Members (People/Companies) - Requires pivot table(s) or polymorphic relation
    // public function members(): MorphToMany { ... } or separate people()/companies() BelongsToMany

    // --- End Relationships ---


    // --- Accessors & Mutators ---

    // Accessor for logo (combines path and url)
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
                // Ensure placeholder exists in public/images
                return asset('images/default-logo-placeholder.png');
            }
        );
    }

    // Accessor for like count
    protected function likeCount(): Attribute
    {
        return Attribute::make(get: fn() => $this->likes()->count());
    }

    // --- End Accessors & Mutators ---
}
