<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo; // Import MorphTo

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // likeable_id and likeable_type are handled by morphs
    ];

    // Relationship to the item being liked (Person or Company)
    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    // Relationship to the user who liked the item
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}