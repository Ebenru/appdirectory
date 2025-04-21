<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany; // For comment likes
use Illuminate\Database\Eloquent\Relations\HasMany;   // For replies
use Illuminate\Database\Eloquent\Casts\Attribute;    // For like count

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commentable_id',
        'commentable_type',
        'text',
        'parent_id', // Allow mass assignment for replies
    ];

    // Relationship to the user who wrote the comment
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to the item being commented on (Person or Company)
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    // --- Comment Likes ---
    /**
     * Get all the likes for this comment.
     * Note: We are making Comment itself "likeable" here.
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable'); // Comment is the 'likeable'
    }

    /**
     * Accessor for the like count on the comment.
     */
    protected function likeCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->likes()->count(),
        );
    }
    // --- End Comment Likes ---


    // --- Comment Threading ---
    /**
     * Relationship to the parent comment (if it's a reply).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Relationship to child comments (replies to this comment).
     */
    public function replies(): HasMany
    {
        // Eager load nested replies and their likes/user if needed frequently
        // return $this->hasMany(Comment::class, 'parent_id')->with('replies', 'user', 'likes');
        return $this->hasMany(Comment::class, 'parent_id');
    }
    // --- End Comment Threading ---
}