<?php

namespace App\Models;

use App\Models\Traits\HasCommonAttributes; // Import trait
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Country extends Model
{
    use HasFactory, HasCommonAttributes; // Use trait for slug

    protected $fillable = [
        'name',
        'slug',
        'iso_code',
        'region',
        'flag_icon_url',
        'calling_code',
    ];

    // --- Implementation for HasCommonAttributes ---
    protected function getNameAttributeForSlug(): ?string
    {
        return 'name'; // Use country name for slug
    }
    // --- End Implementation ---

    // --- Relationships ---

    /**
     * Get people associated with this country (e.g., headquartered).
     */
    public function people(): HasMany
    {
        return $this->hasMany(Person::class); // Based on country_id in people table
    }

    /**
     * Get companies associated with this country.
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class); // Based on country_id in companies table
    }

    /**
     * Get people who have this country as a nationality.
     */
    public function nationals(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'country_person_pivot');
    }

    // Add relationships for Events, Organizations etc. later if needed
    // public function events(): HasMany { ... }
    // public function organizations(): HasMany { ... }

    // --- ADD Relationships to New Entities ---
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class); // Based on country_id in groups table
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class); // Based on country_id in organizations table
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class); // Based on country_id in events table
    }
    // --- End Relationships ---
}
