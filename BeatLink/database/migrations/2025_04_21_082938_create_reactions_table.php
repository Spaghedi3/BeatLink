<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('track_id')->constrained('tracks')->onDelete('cascade');
            $table->enum('reaction', ['love', 'hate']);
            $table->timestamps();

            $table->unique(['owner_id', 'user_id', 'track_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
