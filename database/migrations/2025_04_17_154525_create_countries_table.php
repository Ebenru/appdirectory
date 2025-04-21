<?php

// xxxx_create_countries_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Official country name
            $table->string('slug')->unique(); // URL-friendly slug
            $table->string('iso_code', 2)->unique(); // ISO 3166-1 alpha-2
            $table->string('region')->nullable()->index(); // e.g., Europe, Asia
            $table->string('flag_icon_url')->nullable(); // URL to a flag image/SVG
            // Add other fields like calling_code if needed
            // $table->string('calling_code')->nullable();
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        // Before dropping countries, drop FK constraints from other tables
        // Note: This might require manually listing tables or disabling FK checks
        // For simplicity, we rely on the down() method of the *other* migration
        // to drop its own FK first when rolling back fully.
        Schema::dropIfExists('countries');
    }
};
