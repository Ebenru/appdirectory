<?php

// ****_create_comments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who wrote the comment
            $table->morphs('commentable'); // Item being commented on (Person or Company)
            $table->text('text'); // The comment content
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade'); // For threading (replying to another comment)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};