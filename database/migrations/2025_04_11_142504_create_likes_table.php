<?php

// ****_create_likes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who liked
            $table->morphs('likeable'); // Creates likeable_id (unsignedBigInt) and likeable_type (string)
            $table->timestamps();

            // Prevent a user from liking the same item multiple times
            $table->unique(['user_id', 'likeable_id', 'likeable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
