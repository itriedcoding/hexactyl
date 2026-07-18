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
        Schema::create('server_templates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('author')->nullable();
            $table->string('version')->default('1.0.0');
            $table->boolean('is_public')->default(false);
            $table->boolean('is_default')->default(false);

            // Server configuration
            $table->unsignedInteger('egg_id');
            $table->unsignedInteger('nest_id');
            $table->unsignedInteger('location_id')->nullable();
            $table->string('docker_image')->nullable();
            $table->json('startup')->nullable();
            $table->json('environment')->nullable();
            $table->json('limits')->nullable();
            $table->json('feature_limits')->nullable();
            $table->json('allocations')->nullable();
            $table->json('startup_commands')->nullable();
            $table->json('post_install_commands')->nullable();

            // Metadata
            $table->json('tags')->nullable();
            $table->string('icon')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('egg_id')->references('id')->on('eggs')->onDelete('cascade');
            $table->foreign('nest_id')->references('id')->on('nests')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_templates');
    }
};
