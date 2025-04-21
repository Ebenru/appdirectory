<?php

namespace App\Models;

use App\Models\Traits\HasCommonAttributes; // Import trait
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use HasFactory, HasCommonAttributes; // Use trait for slug

    protected $fillable = ['name', 'slug'];

    // --- Implementation for HasCommonAttributes ---
    protected function getNameAttributeForSlug(): ?string
    {
        return 'name'; // Use tag name for slug
    }
    // --- End Implementation ---


    // --- Relationships (Inverse Polymorphic) ---

    public function people(): MorphToMany
    {
        return $this->morphedByMany(Person::class, 'taggable');
    }

    public function companies(): MorphToMany
    {
        return $this->morphedByMany(Company::class, 'taggable');
    }

    // Add relationships for Groups, Organizations, Events later
    // public function groups(): MorphToMany { return $this->morphedByMany(Group::class, 'taggable'); }
    // public function organizations(): MorphToMany { ... }
    // public function events(): MorphToMany { ... }

    // --- End Relationships ---
}
