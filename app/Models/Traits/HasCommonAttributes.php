<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str; // Import Str helper for slugs

trait HasCommonAttributes
{
    /**
     * Boot the trait to automatically generate slugs if needed.
     * Requires the model to have a 'name' or similar attribute to slugify
     * and a 'slug' column in the database.
     */
    protected static function bootHasCommonAttributes(): void
    {
        static::creating(function ($model) {
            // Check if slug is empty AND if the model has a 'name' , 'groupName' , 'orgName' etc.
            $nameAttribute = $model->getNameAttributeForSlug(); // Helper method needed
            if (empty($model->slug) && $nameAttribute) {
                $model->slug = Str::slug($model->{$nameAttribute});
                // Ensure uniqueness (optional, but good practice)
                $count = static::where('slug', $model->slug)->count();
                if ($count > 0) {
                    // Find existing models with the same base slug
                    $baseSlug = $model->slug;
                    $similarSlugsCount = static::where('slug', 'LIKE', $baseSlug . '-%')->count();
                    $model->slug = $baseSlug . '-' . ($similarSlugsCount + 1);
                }
            }
        });

        static::updating(function ($model) {
            // Optionally regenerate slug if name changes and slug isn't explicitly set
            $nameAttribute = $model->getNameAttributeForSlug();
            if ($model->isDirty($nameAttribute) && !$model->isDirty('slug') && $nameAttribute) {
                $model->slug = Str::slug($model->{$nameAttribute});
                // Add uniqueness check for updates too if necessary
                // ... (similar uniqueness logic as above, excluding the current model ID) ...
            }
        });
    }

    /**
     * Define which attribute to use for auto-slug generation.
     * Models using this trait should implement this method.
     * Example: return 'fullName'; or return 'groupName';
     */
    abstract protected function getNameAttributeForSlug(): ?string;


    /**
     * Cast the sources attribute to an array.
     */
    protected function sources(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true) ?? [], // Decode or return empty array
            set: fn($value) => json_encode($value), // Encode when setting
        );
    }

    /**
     * Cast the social_media attribute to an array/object.
     */
    protected function socialMedia(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true) ?? [], // Decode or return empty array
            set: fn($value) => json_encode($value), // Encode when setting
        );
    }

    /**
     * Get the route key for the model.
     * Use 'slug' for Route Model Binding instead of ID.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
