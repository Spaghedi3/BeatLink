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
        Schema::create('track_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('track_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('total_listen_seconds')->default(0);
            $table->unsignedInteger('love_count')->default(0);
            $table->unsignedInteger('hate_count')->default(0);
            $table->timestamps();

            $table->unique('track_id');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('track_stats');
    }
};
