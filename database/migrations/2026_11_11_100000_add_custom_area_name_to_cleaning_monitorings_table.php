<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cleaning_monitorings', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->foreignId('area_id')->nullable()->change();
            $table->foreign('area_id')->references('id')->on('areas')->cascadeOnDelete();
            $table->string('custom_area_name')->nullable()->after('area_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('cleaning_monitorings')->whereNotNull('custom_area_name')->exists()) {
            throw new RuntimeException('Cannot remove custom area names while monitoring records use them.');
        }

        Schema::table('cleaning_monitorings', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropColumn('custom_area_name');
            $table->foreignId('area_id')->nullable(false)->change();
            $table->foreign('area_id')->references('id')->on('areas')->cascadeOnDelete();
        });
    }
};
