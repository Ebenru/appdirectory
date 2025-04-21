<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Common attribute (replacing orgName)
            $table->string('slug')->unique(); // Common
            $table->text('description')->nullable(); // Common
            $table->string('type')->nullable(); // Specific (NGO, Gov Body, etc.)
            $table->year('foundingYear')->nullable(); // Specific (use year type)
            $table->string('scope')->nullable(); // Specific (e.g., 'International', 'National', 'Local')
            $table->string('logo_path', 2048)->nullable(); // Common-like
            $table->string('logo_url', 500)->nullable(); // Common-like
            $table->string('website_url')->nullable(); // Common
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete(); // Common
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'archived'])->default('pending'); // Common
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // Common
            $table->json('sources')->nullable(); // Common
            $table->json('social_media')->nullable(); // Common
            // $table->integer('misinformation_reports')->default(0); // Common
            // Add campaign_caller, key_achievements, featured_article_url later
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
