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
        Schema::create('topofthetops', function (Blueprint $table) {
            $table->string('game_id')->primary();
            $table->string('game_name')->nullable()->default(null);
            $table->string('user_name')->nullable()->default(null);
            $table->integer('total_videos')->nullable()->default(null);
            $table->integer('total_views')->nullable()->default(null);
            $table->string('most_viewed_title')->nullable()->default(null);
            $table->integer('most_viewed_views')->nullable()->default(null);
            $table->string('most_viewed_duration')->nullable()->default(null);
            $table->string('most_viewed_created_at')->nullable()->default(null);
            $table->date('last_updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topofthetops');
    }
};
