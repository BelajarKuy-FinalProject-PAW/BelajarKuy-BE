<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->string('no_telp')->nullable()->after('email');
            $table->string('profile_photo_path', 2048)->nullable()->after('no_telp');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'no_telp', 'profile_photo_path']);
        });
    }
};