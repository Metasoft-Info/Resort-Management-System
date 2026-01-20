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
        Schema::table('resort_info', function (Blueprint $table) {
            $table->string('resort_name')->nullable()->after('id');
            $table->string('resort_tagline')->nullable()->after('resort_name');
            $table->text('footer_description')->nullable()->after('mission_text');
            $table->text('copyright_text')->nullable()->after('social_links');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resort_info', function (Blueprint $table) {
            $table->dropColumn(['resort_name', 'resort_tagline', 'footer_description', 'copyright_text']);
        });
    }
};
