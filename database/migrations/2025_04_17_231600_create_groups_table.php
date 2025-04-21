<?php
// xxxx_create_groups_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Common attribute (replacing groupName)
            $table->string('slug')->unique(); // Common attribute
            $table->text('description')->nullable(); // Common attribute
            $table->string('industry')->nullable(); // Specific attribute
            $table->string('logo_path', 2048)->nullable(); // Common-like attribute
            $table->string('logo_url', 500)->nullable(); // Common-like attribute
            $table->string('website_url')->nullable(); // Common attribute
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete(); // Common attribute
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'archived'])->default('pending'); // Common attribute (using expanded list)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // Common attribute
            $table->json('sources')->nullable(); // Common attribute
            $table->json('social_media')->nullable(); // Common attribute
            // $table->integer('misinformation_reports')->default(0); // Common - If implementing reports
            // Add campaign_caller, key_achievements, featured_article_url later
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
