<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('beat_type', 'track_type');
    }

    public function down(): void
    {
        Schema::rename('track_type', 'beat_type');
    }
};
