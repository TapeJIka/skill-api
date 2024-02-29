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
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('artwork')->nullable();
            $table->string('title')->nullable();
            $table->string('file');
            $table->string('artist')->nullable();
            $table->string('composer')->nullable();
            $table->string('album')->nullable();
            $table->string('track_number')->nullable();
            $table->json('genres')->nullable();
            $table->time('playtime')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
