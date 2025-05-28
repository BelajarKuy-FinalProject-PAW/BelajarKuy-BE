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
        Schema::create('topic_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // 'constrained()' akan otomatis mengambil nama tabel dari model User ('users')
            // 'onDelete('cascade')' berarti jika user dihapus, entri di tabel pivot ini juga akan dihapus

            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            // 'constrained()' akan otomatis mengambil nama tabel dari model Topic ('topics')

            // Menetapkan composite primary key untuk memastikan kombinasi user_id dan topic_id unik
            $table->primary(['user_id', 'topic_id']);

            // Tabel pivot sederhana ini biasanya tidak memerlukan timestamps (created_at, updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_user');
    }
};