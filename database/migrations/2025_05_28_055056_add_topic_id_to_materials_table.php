<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('materials', function (Blueprint $table) {
            $table->foreignId('topic_id')->nullable()->after('description') // Sesuaikan posisi 'after'
                  ->constrained()->onDelete('set null'); // atau onDelete('cascade')
        });
    }
    public function down(): void {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['topic_id']); // Hapus foreign key constraint dulu
            $table->dropColumn('topic_id');
        });
    }
};