<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('datapasien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pasien')->constrained('pasien')->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan'); // 1-12
            $table->decimal('bb', 5, 2)->nullable(); // kg
            $table->decimal('tb', 5, 1)->nullable(); // cm
            $table->string('keterangan')->nullable(); // lulus balita, pulkam, pindah, dll
            $table->timestamps();

            $table->unique(['id_pasien', 'tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datapasien');
    }
};