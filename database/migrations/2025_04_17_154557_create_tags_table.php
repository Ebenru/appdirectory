<?php

// xxxx_create_tags_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tag display name (e.g., "Labor Rights")
            $table->string('slug')->unique(); // URL-friendly version (e.g., "labor-rights")
            // $table->string('description')->nullable(); // Optional description
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
