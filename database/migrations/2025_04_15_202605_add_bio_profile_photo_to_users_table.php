<?php

// xxxx_add_bio_profile_photo_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('is_admin'); // Add bio field
            $table->string('profile_photo_path', 2048)->nullable()->after('bio'); // Add path for photo
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
             $table->dropColumn(['bio', 'profile_photo_path']);
        });
    }
};