<?php

// xxxx_create_taggables_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taggables', function (Blueprint $table) {
            $table->foreignId('tag_id')->constrained()->onDelete('cascade'); // Link to tags table
            $table->morphs('taggable'); // Creates taggable_id and taggable_type

            // Define primary key as combination of the three columns
            $table->primary(['tag_id', 'taggable_id', 'taggable_type']);

            // Add indexes for faster lookups (optional but recommended)
            $table->index(['taggable_id', 'taggable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taggables');
    }
};
