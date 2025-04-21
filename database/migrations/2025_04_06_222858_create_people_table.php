<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('fullName');
            $table->string('title')->nullable();
            $table->string('picture_url', 500)->nullable(); // Consider Image upload later
            $table->text('description')->nullable();
            $table->string('pplCategory', 50)->nullable()->index(); // e.g., 'tech', 'arts'
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->integer('rank')->default(0);
            $table->foreignId('submitted_by_id')->nullable()->constrained('users')->onDelete('set null'); // Link to user who submitted
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null'); // Link to user who approved
            $table->timestamp('approved_at')->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
