<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Common (replacing eventName)
            $table->string('slug')->unique(); // Common
            $table->text('description')->nullable(); // Common
            $table->dateTime('startDate')->nullable(); // Specific (use dateTime)
            $table->dateTime('endDate')->nullable(); // Specific
            $table->text('location')->nullable(); // Specific (simple text for now)
            $table->boolean('is_virtual')->default(false); // Specific (if applicable)
            $table->string('eventType')->nullable(); // Specific (Protest, Conference etc)
            $table->string('featured_image_path', 2048)->nullable(); // Specific image path
            $table->string('featured_image_url', 500)->nullable(); // Specific image URL
            $table->string('website_url')->nullable(); // Common (e.g., event registration page)
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete(); // Common (Location Country)
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'archived', 'cancelled'])->default('pending'); // Common (added cancelled)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // Common
            $table->json('sources')->nullable(); // Common
            $table->json('social_media')->nullable(); // Common (e.g., event hashtag)
            // $table->integer('misinformation_reports')->default(0); // Common
            // Add campaign_caller, key_achievements, featured_article_url later
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
