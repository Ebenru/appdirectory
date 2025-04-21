<?php

namespace App\Models\Traits;

use App\Models\Tag; // Import Tag model
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Taggable
{
    /**
     * Get all the tags associated with this entity.
     */
    public function tags(): MorphToMany
    {
        // Assumes the pivot table is 'taggables' and the relationship name is 'taggable'
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Scope a query to only include models matching the given tags (by slug).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array|string $tags Slugs of the tags
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAnyTags($query, array|string $tags)
    {
        $tagSlugs = is_array($tags) ? $tags : [$tags];

        return $query->whereHas('tags', function ($q) use ($tagSlugs) {
            $q->whereIn('slug', $tagSlugs);
        });
    }

    /**
     * Scope a query to only include models matching ALL given tags (by slug).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $tags Slugs of the tags
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAllTags($query, array $tags)
    {
        foreach ($tags as $tagSlug) {
            $query->whereHas('tags', function ($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }
        return $query;
    }
}
