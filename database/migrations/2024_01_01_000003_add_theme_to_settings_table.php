<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('active_theme')->default('hexactyl')->after('value')->nullable();
        });

        DB::table('settings')->insertOrIgnore([
            'key' => 'settings::app:active_theme',
            'value' => 'hexactyl',
            'active_theme' => 'hexactyl',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('active_theme');
        });
    }
};
