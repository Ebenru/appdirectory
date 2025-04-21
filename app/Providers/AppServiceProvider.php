<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Person;
use App\Models\Company;
use App\Models\Group;
use App\Models\Organization;
use App\Models\Event;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Tag;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'person' => Person::class,
            'company' => Company::class,
            'group' => Group::class,
            'organization' => Organization::class,
            'event' => Event::class,
            'comment' => Comment::class,
            'like' => Like::class,
            'tag' => Tag::class,
        ]);
    }
}
