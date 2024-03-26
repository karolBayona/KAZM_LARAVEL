<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('token', function (Blueprint $table) {
            $table->string('token',191)->primary(); // Hace que token sea la clave primaria.
            $table->timestamp('created_at')->useCurrent(); // Añade manualmente solo created_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
};