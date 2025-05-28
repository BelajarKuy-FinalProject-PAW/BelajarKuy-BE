<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Topic; // Import model Topic
use Illuminate\Support\Str; // Untuk Str::slug()

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = [
            [
                'name' => 'Pengenalan Website Builder',
                'description' => 'Mengenal berbagai platform pembuat website tanpa koding.'
            ],
            [
                'name' => 'Desain Landing Page Efektif (No-Code)',
                'description' => 'Cara membuat halaman arahan yang menarik dan konversi tinggi.'
            ],
            [
                'name' => 'Membuat Toko Online Cepat',
                'description' => 'Langkah demi langkah membangun toko online tanpa coding.'
            ],
            [
                'name' => 'Dasar Desain Visual untuk Web',
                'description' => 'Prinsip desain yang mudah diterapkan untuk website non-coder.'
            ],
            [
                'name' => 'SEO untuk Pemula (Platform No-Code)',
                'description' => 'Optimasi mesin pencari untuk website yang dibuat tanpa coding.'
            ],
        ];

        foreach ($topics as $topicData) {
            Topic::updateOrCreate(
                ['name' => $topicData['name']], // Kriteria untuk mencari (jika nama ini sudah ada, record akan diupdate)
                [                               // Data yang akan dimasukkan atau diupdate
                    'slug' => Str::slug($topicData['name']), // Pastikan slug juga di-generate atau di-update
                    'description' => $topicData['description']
                ]
            );
        }
    }
}