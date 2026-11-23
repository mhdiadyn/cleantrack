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
        Schema::create('floor_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('floor')->unique();
            $table->string('file_path');
            $table->timestamps();
        });

        Schema::table('areas', function (Blueprint $table) {
            $table->renameColumn('photo_360', 'photo');
        });

        Schema::table('cleaning_monitorings', function (Blueprint $table) {
            $table->renameColumn('photo_360', 'room_photo');
        });

        Schema::table('areas', function (Blueprint $table) {
            $table->dropColumn('photo_type');
        });

        Schema::table('cleaning_monitorings', function (Blueprint $table) {
            $table->dropColumn('photo_360_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->string('photo_type')->default('standard');
        });

        Schema::table('cleaning_monitorings', function (Blueprint $table) {
            $table->string('photo_360_type')->default('standard');
        });

        Schema::table('areas', function (Blueprint $table) {
            $table->renameColumn('photo', 'photo_360');
        });

        Schema::table('cleaning_monitorings', function (Blueprint $table) {
            $table->renameColumn('room_photo', 'photo_360');
        });

        Schema::dropIfExists('floor_plans');
    }
};
