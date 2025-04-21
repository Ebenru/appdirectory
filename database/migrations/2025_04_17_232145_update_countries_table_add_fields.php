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
        Schema::table('countries', function (Blueprint $table) {
            // Verify 'region' exists, add if missing
            if (!Schema::hasColumn('countries', 'region')) {
                $table->string('region')->nullable()->after('iso_code');
            }
            // Add 'calling_code' if desired
            if (!Schema::hasColumn('countries', 'calling_code')) {
                $table->string('calling_code')->nullable()->after('region');
            }
            // Verify 'flag_icon_url' exists, add if missing
            if (!Schema::hasColumn('countries', 'flag_icon_url')) {
                $table->string('flag_icon_url')->nullable()->after('calling_code'); // Adjust 'after' if needed
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            // Only drop columns if they were added in THIS migration's up()
            if (Schema::hasColumn('countries', 'calling_code')) {
                $table->dropColumn('calling_code');
            }
        });
    }
};
