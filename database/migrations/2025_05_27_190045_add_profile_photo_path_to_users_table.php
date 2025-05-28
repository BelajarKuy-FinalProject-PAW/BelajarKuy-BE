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
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom 'profile_photo_path' setelah kolom 'no_telp' (atau sesuaikan)
            // Tipe data string, dengan panjang maksimal 2048 karakter (cukup untuk path file)
            // Nullable berarti kolom ini boleh kosong (tidak semua user wajib punya foto profil)
            $table->string('profile_photo_path', 2048)->nullable()->after('no_telp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom 'profile_photo_path' jika migrasi di-rollback
            $table->dropColumn('profile_photo_path');
        });
    }
};