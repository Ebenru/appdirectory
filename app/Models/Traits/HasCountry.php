<?php

namespace App\Models\Traits;

use App\Models\Country; // Import Country model
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCountry
{
    /**
     * Get the primary country associated with this entity.
     */
    public function country(): BelongsTo
    {
        // Assumes the foreign key column is named 'country_id'
        return $this->belongsTo(Country::class);
    }

    // You could add scopes here later, e.g., scopeInCountry($query, $countryId)
}
