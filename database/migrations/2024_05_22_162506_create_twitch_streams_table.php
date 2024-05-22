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
        Schema::create('twitch_streams', function (Blueprint $table) {
            $table->id('stream_id');
            $table->unsignedBigInteger('streamer_id');
            $table->string('title');
            $table->string('game');
            $table->integer('viewer_count');
            $table->timestamp('started_at');
            $table->timestamps();

            $table->foreign('streamer_id')->references('streamer_id')->on('twitch_streamers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twitch_streams');
    }
};
