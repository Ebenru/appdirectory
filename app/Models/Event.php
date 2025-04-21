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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// --- Other Imports ---
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon; // For date handling if needed outside casts

class Event extends Model
{
    // --- Use Traits ---
    use HasFactory, HasCountry, HasCommonAttributes, Taggable;

    // --- Fillable Properties ---
    protected $fillable = [
        'name', // Common
        'slug', // Common
        'description', // Common
        'startDate', // Specific
        'endDate', // Specific
        'location', // Specific (Text for now)
        'is_virtual', // Specific
        'eventType', // Specific
        'featured_image_path', // Specific
        'featured_image_url', // Specific
        'website_url', // Common
        'country_id', // Common (Location Country)
        'status', // Common
        'created_by', // Common
        'sources', // Common (JSON)
        'social_media', // Common (JSON)
        'key_achievements', // Common
        'featured_article_url', // Common
    ];

    // --- Attribute Casting ---
    protected $casts = [
        'sources' => 'array',
        'social_media' => 'array',
        'status' => 'string',
        'startDate' => 'datetime', // Cast dates
        'endDate' => 'datetime',
        'is_virtual' => 'boolean',
    ];

    // --- Implementation for HasCommonAttributes ---
    protected function getNameAttributeForSlug(): ?string
    {
        return 'name'; // Use 'name' field
    }
    // --- End Implementation ---


    // --- Relationships ---

    // Relationships added by Traits:
    // country() from HasCountry
    // tags() from Taggable

    // Common Polymorphic Relationships (defined explicitly or via Traits)
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
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // TODO: Define Specific Relationships for Event
    // Example: Sponsors (Companies/Organizations) - Polymorphic Many-to-Many
    // public function sponsors(): MorphToMany
    // {
    //    return $this->morphToMany(Model::class, 'sponsorable', 'event_sponsors') // Use specific models if possible
    //               ->withPivot('sponsorship_level'); // Example pivot data
    // }

    // Example: Attendees (People) - Many-to-Many
    // public function attendees(): BelongsToMany
    // {
    //    return $this->belongsToMany(Person::class, 'event_person'); // Assumes 'event_person' pivot
    // }

    // Example: Related Campaigns (if Campaign is another model)
    // public function relatedCampaigns(): BelongsToMany { ... }

    // --- End Relationships ---


    // --- Accessors & Mutators ---

    // Accessor for featured image (combines path and url)
    protected function displayFeaturedImageUrl(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if (!empty($attributes['featured_image_path']) && Storage::disk('public')->exists($attributes['featured_image_path'])) {
                    return Storage::disk('public')->url($attributes['featured_image_path']);
                }
                if (!empty($attributes['featured_image_url'])) {
                    return $attributes['featured_image_url'];
                }
                // Ensure placeholder exists in public/images
                return asset('images/default-event-placeholder.png');
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
