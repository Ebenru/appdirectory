<?php

// xxxx_add_common_fields_to_entities_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- Add to People Table ---
        Schema::table('people', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('fullName'); // Unique slug for URLs
            $table->foreignId('country_id')->nullable()->after('slug')->constrained('countries')->nullOnDelete(); // Link to countries table (create this table next!)
            $table->json('sources')->nullable()->after('description'); // Store array of source URLs/text
            $table->json('social_media')->nullable()->after('sources'); // Store key-value pairs (e.g., {"twitter": "url", "linkedin": "url"})
            $table->string('photo_path', 2048)->nullable()->after('picture_url');

            // Add index for faster country lookups
            $table->index('country_id');
            // Update status enum if needed (e.g., add 'draft', 'archived') - Optional
            // $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'archived'])->default('pending')->change();
        });

        // --- Add to Companies Table ---
        Schema::table('companies', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('displayName');
            $table->foreignId('country_id')->nullable()->after('slug')->constrained('countries')->nullOnDelete(); // Assuming primary country for company HQ
            $table->json('sources')->nullable()->after('description');
            $table->json('social_media')->nullable()->after('sources');
            $table->string('logo_path', 2048)->nullable()->after('logo_url');
            // Add indexes
            $table->index('country_id');
            // Update status enum if needed - Optional
            // $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'archived'])->default('pending')->change();
        });

        // --- Add/Modify on Users Table (Optional) ---
        Schema::table('users', function (Blueprint $table) {
            // Add country if users relate to a primary country
            // $table->foreignId('country_id')->nullable()->after('is_admin')->constrained('countries')->nullOnDelete();
            // $table->index('country_id');

            // Rename profile_photo_path for consistency? Optional.
            // $table->renameColumn('profile_photo_path', 'image_path');
        });

        // NOTE: We will add these common columns to NEW tables (groups, orgs, events)
        // directly in THEIR respective migrations later.
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            // Drop foreign key constraint first if it exists
            $table->dropForeign(['country_id']);
            $table->dropColumn(['slug', 'country_id', 'sources', 'social_media']);
            // Revert status enum change if done - Optional
            // $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn(['slug', 'country_id', 'sources', 'social_media']);
            // Revert status enum change if done - Optional
            // $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            // if ($table->hasColumn('country_id')) { // Check before dropping
            //     $table->dropForeign(['country_id']);
            //     $table->dropColumn('country_id');
            // }
            // if ($table->hasColumn('image_path')) {
            //     $table->renameColumn('image_path', 'profile_photo_path');
            // }
        });
    }
};
