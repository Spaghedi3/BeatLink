<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('track_tag', function (Blueprint $table) {
            $table->renameColumn('beat_id', 'track_id');
        });
    }

    public function down(): void
    {
        Schema::table('track_tag', function (Blueprint $table) {
            $table->renameColumn('track_id', 'beat_id');
        });
    }
};
