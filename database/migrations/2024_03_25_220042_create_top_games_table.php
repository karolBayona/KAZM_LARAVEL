<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopGamesTable extends Migration
{
    public function up()
    {
        Schema::create('top_games', function (Blueprint $table) {
            $table->string('game_id')->unique();
            $table->string('game_name'); // Nombre del juego
        });
    }

    public function down()
    {
        Schema::dropIfExists('top_games');
    }
}
