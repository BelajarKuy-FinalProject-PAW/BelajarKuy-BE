<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
// Kita tidak lagi butuh Topic model di sini

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Contoh Materi untuk Kategori "No-Code Development"
        Material::updateOrCreate(
            ['name' => 'Memilih Platform Website Builder Terbaik untuk Pemula'], // Ganti 'title' menjadi 'name'
            [
                'category' => 'No-Code Development', // Tambahkan nilai untuk kategori
                'description' => 'Perbandingan fitur dan kemudahan penggunaan berbagai platform no-code.',
            ]
        );
        Material::updateOrCreate(
            ['name' => 'Dasar-Dasar Antarmuka Platform Populer'], // Ganti 'title' menjadi 'name'
            [
                'category' => 'No-Code Development',
                'description' => 'Navigasi dan pengenalan tool dasar di platform populer.',
            ]
        );

        // Contoh Materi untuk Kategori "Desain Web"
        Material::updateOrCreate(
            ['name' => 'Anatomi Landing Page yang Mengkonversi'], // Ganti 'title' menjadi 'name'
            [
                'category' => 'Desain Web',
                'description' => 'Elemen-elemen penting yang harus ada di landing page Anda.',
            ]
        );
        Material::updateOrCreate(
            ['name' => 'Tutorial: Membuat Landing Page Pertama Anda'], // Ganti 'title' menjadi 'name'
            [
                'category' => 'Desain Web',
                'description' => 'Langkah praktis membuat landing page dari awal hingga publish.',
            ]
        );
        
        // Contoh Materi untuk Kategori "E-commerce"
        Material::updateOrCreate(
            ['name' => 'Setup Awal Toko Online Tanpa Coding'], // Ganti 'title' menjadi 'name'
            [
                'category' => 'E-commerce',
                'description' => 'Konfigurasi dasar untuk toko online Anda di platform no-code.',
            ]
        );
        Material::updateOrCreate(
            ['name' => 'Manajemen Produk dan Inventaris'], // Ganti 'title' menjadi 'name'
            [
                'category' => 'E-commerce',
                'description' => 'Cara menambahkan produk, mengatur harga, dan stok.',
            ]
        );
        Material::updateOrCreate(
            ['name' => 'Integrasi Pembayaran untuk Toko Online Anda'], // Ganti 'title' menjadi 'name'
            [
                'category' => 'E-commerce',
                'description' => 'Menghubungkan toko Anda dengan payment gateway populer.',
            ]
        );
    }
}