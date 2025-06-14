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
    Schema::create('sub_materials', function (Blueprint $table) {
        $table->id(); // ID Sub Materi
        $table->foreignId('material_id')->constrained()->onDelete('cascade'); // ID Materi (FK)
        $table->string('unique_code')->unique()->comment('Contoh: AIDS1'); // ID Unik
        $table->string('title'); // Judul sub-materi, misal: "Visualisasi Data"
        $table->string('file_path')->nullable(); // Path ke file PDF
        $table->string('video_path')->nullable(); // Path atau URL ke file video
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('sub_materials');
    }
};
