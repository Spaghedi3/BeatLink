<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('beat_type_stats');
    }

    public function down(): void
    {
        Schema::create('beat_type_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('type');
            $table->integer('count')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'type']);
        });
    }
};
