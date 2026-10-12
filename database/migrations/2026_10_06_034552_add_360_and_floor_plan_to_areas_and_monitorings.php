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
            $table->string('floor')->default('Lantai 1')->after('location');
            $table->float('pos_x')->nullable()->after('floor')->comment('Posisi X pada Denah (persentase 0-100)');
            $table->float('pos_y')->nullable()->after('pos_x')->comment('Posisi Y pada Denah (persentase 0-100)');
            $table->string('photo_360')->nullable()->after('pos_y');
        });

        Schema::table('cleaning_monitorings', function (Blueprint $table) {
            $table->string('photo_360')->nullable()->after('photo_after');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->dropColumn(['floor', 'pos_x', 'pos_y', 'photo_360']);
        });

        Schema::table('cleaning_monitorings', function (Blueprint $table) {
            $table->dropColumn('photo_360');
        });
    }
};
