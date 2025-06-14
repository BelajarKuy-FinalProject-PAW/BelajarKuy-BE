<?php
// .../_create_materials_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id(); // ID Materi
            $table->string('name'); // Nama Materi (sebelumnya title)
            $table->string('category'); // Kategori Materi
            $table->text('description')->nullable(); // Deskripsi singkat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};