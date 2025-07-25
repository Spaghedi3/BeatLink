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
        Schema::table('tracks', function (Blueprint $table) {
            $table->integer('bpm')->nullable();
            $table->string('key')->nullable();
            $table->string('scale')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tracks', function (Blueprint $table) {
            $table->dropColumn(['bpm', 'key', 'scale']);
        });
    }
};
