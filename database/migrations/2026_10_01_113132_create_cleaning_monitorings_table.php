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
        Schema::create('cleaning_monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->enum('cleanliness_condition', ['Bersih', 'Cukup Bersih', 'Kotor', 'Sangat Kotor'])->default('Bersih');
            $table->enum('status', ['Belum Diperiksa', 'Perlu Dibersihkan', 'Sedang Dibersihkan', 'Sudah Dibersihkan'])->default('Sudah Dibersihkan');
            $table->text('notes')->nullable();
            $table->string('photo_before')->nullable();
            $table->string('photo_after')->nullable();
            $table->date('check_date');
            $table->time('check_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cleaning_monitorings');
    }
};
