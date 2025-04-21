<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // List of tables to add these fields to
    private $tables = ['people', 'companies', 'groups', 'organizations', 'events'];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) { // Pass $tableName to closure
                // Add key_achievements after description (likely exists on all)
                if (Schema::hasColumn($tableName, 'description')) {
                    $table->text('key_achievements')->nullable()->after('description');
                } else {
                    // Fallback if description doesn't exist (add it without 'after')
                    $table->text('key_achievements')->nullable();
                }

                // Add featured_article_url conditionally after website_url or another common field
                if (Schema::hasColumn($tableName, 'website_url')) {
                    $table->string('featured_article_url', 500)->nullable()->after('website_url');
                } elseif (Schema::hasColumn($tableName, 'social_media')) { // Fallback: after social_media
                    $table->string('featured_article_url', 500)->nullable()->after('social_media');
                } elseif (Schema::hasColumn($tableName, 'sources')) { // Fallback: after sources
                    $table->string('featured_article_url', 500)->nullable()->after('sources');
                } else {
                    // Fallback: just add it if other common fields are missing
                    $table->string('featured_article_url', 500)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table)  use ($tableName) {
                // Check before dropping in case they weren't added
                $columnsToDrop = [];
                if (Schema::hasColumn($tableName, 'key_achievements')) {
                    $columnsToDrop[] = 'key_achievements';
                }
                if (Schema::hasColumn($tableName, 'featured_article_url')) {
                    $columnsToDrop[] = 'featured_article_url';
                }
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};
