<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\Database\Eloquent\Casts\Attribute; // Import Attribute cast
use Illuminate\Database\Eloquent\Model; // Import Model

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio', 
        'profile_photo_path', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
     * Get all the likes made by the user.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }
    /**
     * Check if the user has liked a specific model instance.
     *
     * @param Model $likeable The model instance (Person or Company) to check.
     * @return bool
     */
    public function hasLiked(Model $likeable): bool
    {
        if (!$likeable->exists) { // Ensure the model exists
            return false;
        }

        // Check if a like exists matching the user and the specific likeable model instance
        return $this->likes()
                    ->where('likeable_id', $likeable->id)
                    ->where('likeable_type', $likeable->getMorphClass()) // Use getMorphClass() for the type string
                    ->exists();
    }

    /**
     * Get all the comments written by the user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    
    // --- Add Accessor for Profile Photo URL ---
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->profile_photo_path
                        // If path exists, generate URL from storage, otherwise use default UI Avatars
                        ? Storage::disk('public')->url($this->profile_photo_path)
                        : $this->defaultProfilePhotoUrl(),
        );
    }  
    
    /**
    * Get the default profile photo URL if no profile photo has been uploaded.
    */
    protected function defaultProfilePhotoUrl(): string
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        // Using UI Avatars service
        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=FFFFFF&background=DC2626'; // Red background
    }

    // --- Add Submission Relationships ---
    public function peopleSubmissions(): HasMany
    {
        return $this->hasMany(Person::class, 'submitted_by_id');
    }

    public function companySubmissions(): HasMany
    {
         return $this->hasMany(Company::class, 'submitted_by_id');
    }
}
