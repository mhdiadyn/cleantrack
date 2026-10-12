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
        Schema::table('areas', function (Blueprint $table) {
            $table->string('photo_type')->default('panorama_360')->after('photo_360')->comment('Tipe foto ruangan: panorama_360 atau standard');
        });

        Schema::table('cleaning_monitorings', function (Blueprint $table) {
            $table->string('photo_360_type')->default('panorama_360')->after('photo_360')->comment('Tipe foto 360/ruangan: panorama_360 atau standard');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->dropColumn('photo_type');
        });

        Schema::table('cleaning_monitorings', function (Blueprint $table) {
            $table->dropColumn('photo_360_type');
        });
    }
};
