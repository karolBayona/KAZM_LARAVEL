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
        Schema::create('twitch_user_streamers', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('streamer_id');
            $table->timestamp('followed_at')->useCurrent();

            $table->timestamps();

            $table->primary(['user_id', 'streamer_id']);

            $table->foreign('user_id')->references('user_id')->on('twitch_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twitch_user_streamers');
    }
};
