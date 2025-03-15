<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('beats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Owner of the beat
            $table->string('name');
            $table->string('file_path'); // For the audio file (e.g., .mp3, .wav)
            $table->string('picture')->nullable(); // Optional picture
            $table->string('tags')->nullable(); // Can be comma-separated or use a pivot table for many-to-many
            $table->string('category')->nullable(); // e.g. instrumental, drumkit, etc.
            $table->string('type_beat')->nullable(); // e.g. the artist's style suggestion
            $table->timestamps(); // includes created_at (date added) and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beats');
    }
};
