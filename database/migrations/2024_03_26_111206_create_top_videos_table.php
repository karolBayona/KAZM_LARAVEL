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
        Schema::create('top_videos', function (Blueprint $table) {
            $table->string('game_id')->nullable()->default(null);
            $table->string('video_id')->primary();
            $table->string('video_title')->nullable()->default(null);
            $table->integer('video_views')->nullable()->default(null);
            $table->string('user_name')->nullable()->default(null);
            $table->string('duration')->nullable()->default(null);
            $table->string('created_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('top_videos');
    }
};
