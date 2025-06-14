<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Topic;
use App\Models\Material;
use App\Models\LearningHistory;
use App\Http\Resources\LearningHistoryResource; // Untuk perbandingan struktur
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Carbon\Carbon; // Untuk manipulasi waktu

class LearningHistoryApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function unauthenticated_user_cannot_get_learning_history(): void
    {
        $response = $this->getJson('/api/user/learning-history');
        $response->assertUnauthorized();
    }

    #[Test]
    public function authenticated_user_can_get_their_learning_history_when_it_is_empty(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user/learning-history');

        $response->assertStatus(200)
                 ->assertJsonStructure([ // Struktur paginasi Laravel default
                     'data',
                     'links' => ['first', 'last', 'prev', 'next'],
                     'meta' => [
                         'current_page', 'from', 'last_page', 'links',
                         'path', 'per_page', 'to', 'total',
                     ],
                 ])
                 ->assertJsonCount(0, 'data'); // Memastikan array 'data' kosong
    }

    #[Test]
    public function authenticated_user_can_get_their_existing_learning_history_with_correct_data_and_order(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Buat beberapa topik dan materi
        $topic = Topic::factory()->create();
        $material1 = Material::factory()->for($topic)->create(['title' => 'Materi Awal']);
        $material2 = Material::factory()->for($topic)->create(['title' => 'Materi Tengah']);
        $material3 = Material::factory()->for($topic)->create(['title' => 'Materi Akhir']);

        // Buat riwayat belajar dengan urutan waktu yang berbeda
        // Controller mengurutkan berdasarkan completed_at DESC, lalu created_at DESC
        $history1 = LearningHistory::factory()->for($user)->for($material1)->create([
            'completed_at' => Carbon::now()->subDays(2), // Paling lama selesai
            'created_at' => Carbon::now()->subHours(2)
        ]);
        $history3 = LearningHistory::factory()->for($user)->for($material3)->create([
            'completed_at' => Carbon::now(), // Paling baru selesai
            'created_at' => Carbon::now()
        ]);
        $history2 = LearningHistory::factory()->for($user)->for($material2)->create([
            'completed_at' => Carbon::now()->subDay(), // Selesai kemarin
            'created_at' => Carbon::now()->subHour()
        ]);
        
        // Buat juga satu riwayat belajar untuk user lain, untuk memastikan tidak ikut terambil
        $otherUser = User::factory()->create();
        LearningHistory::factory()->for($otherUser)->for($material1)->create();


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user/learning-history');
        
        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data') // Mengharapkan 3 riwayat belajar untuk user ini
                 ->assertJsonPath('data.0.id', $history3->id) // Verifikasi urutan (history3 paling baru)
                 ->assertJsonPath('data.1.id', $history2->id)
                 ->assertJsonPath('data.2.id', $history1->id)
                 ->assertJsonPath('data.0.material.title', $material3->title) // Cek juga data materi
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'material' => ['id', 'title'],
                             'completed_at',
                             'recorded_at',
                         ]
                     ],
                     'links',
                     'meta',
                 ]);
    }
    #[Test]
    public function authenticated_user_can_mark_a_material_as_complete(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $material = Material::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/materials/{$material->id}/complete");

        $response->assertStatus(201) // Sesuai controller Anda jika baru dibuat
                 ->assertJsonStructure([
                     'message',
                     'history' => [
                         'id',
                         'material' => ['id', 'title'],
                         'completed_at',
                         'recorded_at',
                     ]
                 ])
                 ->assertJsonPath('message', 'Materi berhasil ditandai sebagai selesai.')
                 ->assertJsonPath('history.material.id', $material->id);

        $this->assertDatabaseHas('learning_histories', [
            'user_id' => $user->id,
            'material_id' => $material->id,
        ]);
        // Bisa juga cek completed_at tidak null jika itu ekspektasinya
        $learningHistory = LearningHistory::where('user_id', $user->id)->where('material_id', $material->id)->first();
        $this->assertNotNull($learningHistory->completed_at);
    }

    #[Test]
    public function marking_a_material_as_complete_again_updates_completed_at(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $material = Material::factory()->create();
        
        // Tandai selesai pertama kali
        $firstCompletionTime = Carbon::now()->subHour();
        LearningHistory::factory()->for($user)->for($material)->create([
            'completed_at' => $firstCompletionTime,
        ]);

        // Beri jeda sedikit untuk memastikan timestamp berbeda
        $this->travel(5)->seconds(); // Majukan waktu 5 detik

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/materials/{$material->id}/complete");

        $response->assertStatus(200) // Sesuai controller Anda jika diupdate
                 ->assertJsonPath('message', 'Waktu penyelesaian materi diperbarui.');

        $learningHistory = LearningHistory::where('user_id', $user->id)->where('material_id', $material->id)->first();
        $this->assertNotNull($learningHistory);
        // Pastikan completed_at lebih baru dari $firstCompletionTime
        $this->assertTrue($learningHistory->completed_at->gt($firstCompletionTime));
    }

    #[Test]
    public function authenticated_user_cannot_mark_non_existent_material_as_complete(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $nonExistentMaterialId = 999;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/materials/{$nonExistentMaterialId}/complete");

        $response->assertNotFound(); // Mengharapkan status 404 karena Route Model Binding
    }

    #[Test]
    public function unauthenticated_user_cannot_mark_material_as_complete(): void
    {
        $material = Material::factory()->create();
        $response = $this->postJson("/api/materials/{$material->id}/complete");

        $response->assertUnauthorized();
    }
}