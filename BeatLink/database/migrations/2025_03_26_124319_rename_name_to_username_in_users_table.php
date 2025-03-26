<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Rename 'name' to 'username'
            $table->renameColumn('name', 'username');

            // Make username unique
            $table->string('username')->unique()->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert back if needed
            $table->renameColumn('username', 'name');
            $table->string('name')->change(); // remove uniqueness
        });
    }
};
