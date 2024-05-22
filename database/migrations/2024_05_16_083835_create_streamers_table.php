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
        Schema::create('streamers', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('login');
            $table->string('display_name');
            $table->string('type')->nullable();
            $table->string('broadcaster_type')->nullable();
            $table->string('description')->nullable();
            $table->string('profile_image_url')->nullable();
            $table->string('offline_image_url')->nullable();
            $table->integer('view_count')->default(0);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('streamers');
    }
};
