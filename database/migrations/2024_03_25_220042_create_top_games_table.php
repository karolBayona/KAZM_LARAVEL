<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create 'top_games' table.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
return new class () extends Migration {
    /**
     * Run the migrations to create the 'top_games' table.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('top_games', function (Blueprint $table) {
            $table->string('game_id')->unique(); // Unique identifier for the game.
            $table->string('game_name');         // Name of the game.
        });
    }

    /**
     * Reverse the migrations by dropping the 'top_games' table.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('top_games');
    }
};
