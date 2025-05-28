<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Topic; // Import model Topic

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil instance topik yang sudah ada berdasarkan namanya (atau slug)
        // Pastikan nama ini sama dengan yang ada di TopicSeeder Anda
        $topicWebBuilder = Topic::where('name', 'Pengenalan Website Builder')->first();
        $topicLandingPage = Topic::where('name', 'Desain Landing Page Efektif (No-Code)')->first();
        $topicTokoOnline = Topic::where('name', 'Membuat Toko Online Cepat')->first();

        // Buat materi-materi yang terkait dengan topik-topik di atas
        if ($topicWebBuilder) {
            Material::updateOrCreate(
                ['title' => 'Memilih Platform Website Builder Terbaik untuk Pemula'],
                [
                    'description' => 'Perbandingan fitur dan kemudahan penggunaan berbagai platform no-code.',
                    'topic_id' => $topicWebBuilder->id,
                ]
            );
            Material::updateOrCreate(
                ['title' => 'Dasar-Dasar Antarmuka [Nama Platform Populer]'],
                [
                    'description' => 'Navigasi dan pengenalan tool dasar di [Nama Platform Populer].',
                    'topic_id' => $topicWebBuilder->id,
                ]
            );
        }

        if ($topicLandingPage) {
            Material::updateOrCreate(
                ['title' => 'Anatomi Landing Page yang Mengkonversi'],
                [
                    'description' => 'Elemen-elemen penting yang harus ada di landing page Anda.',
                    'topic_id' => $topicLandingPage->id,
                ]
            );
            Material::updateOrCreate(
                ['title' => 'Tutorial: Membuat Landing Page Pertama Anda (No-Code)'],
                [
                    'description' => 'Langkah praktis membuat landing page dari awal hingga publish.',
                    'topic_id' => $topicLandingPage->id,
                ]
            );
        }

        if ($topicTokoOnline) {
            Material::updateOrCreate(
                ['title' => 'Setup Awal Toko Online Tanpa Coding'],
                [
                    'description' => 'Konfigurasi dasar untuk toko online Anda di platform no-code.',
                    'topic_id' => $topicTokoOnline->id,
                ]
            );
            Material::updateOrCreate(
                ['title' => 'Manajemen Produk dan Inventaris (No-Code)'],
                [
                    'description' => 'Cara menambahkan produk, mengatur harga, dan stok.',
                    'topic_id' => $topicTokoOnline->id,
                ]
            );
             Material::updateOrCreate(
                ['title' => 'Integrasi Pembayaran untuk Toko Online Anda'],
                [
                    'description' => 'Menghubungkan toko Anda dengan payment gateway populer.',
                    'topic_id' => $topicTokoOnline->id,
                ]
            );
        }
    }
}