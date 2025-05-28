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
        Schema::create('topics', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->string('name')->unique(); // Nama topik, harus unik
            $table->string('slug')->unique()->nullable(); // Untuk URL friendly, bisa di-generate dari nama
            $table->text('description')->nullable(); // Deskripsi singkat tentang topik (opsional)
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};