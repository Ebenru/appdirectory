<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Import DB facade if using raw queries, but Eloquent is cleaner here
use App\Models\Comment; // Import your Comment model
use App\Models\Person;  // Import your Person model
use App\Models\Company; // Import your Company model

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update rows where commentable_type is the Person class name
        Comment::where('commentable_type', Person::class)
               ->update(['commentable_type' => 'person']);

        // Update rows where commentable_type is the Company class name
        Comment::where('commentable_type', Company::class)
               ->update(['commentable_type' => 'company']);

        // Add any other model class mappings here if needed
        // Example:
        // Comment::where('commentable_type', \App\Models\Article::class)
        //        ->update(['commentable_type' => 'article']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert rows where commentable_type is 'person' back to the class name
        Comment::where('commentable_type', 'person')
               ->update(['commentable_type' => Person::class]);

        // Revert rows where commentable_type is 'company' back to the class name
        Comment::where('commentable_type', 'company')
               ->update(['commentable_type' => Company::class]);

        // Add reversals for any other mappings added in up()
        // Example:
        // Comment::where('commentable_type', 'article')
        //        ->update(['commentable_type' => \App\Models\Article::class]);
    }
};