<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Topic; // Kita akan butuh model Topic
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserPreferenceApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function unauthenticated_user_cannot_get_preferences(): void
    {
        // 1. Arrange: (Tidak ada pengguna yang diautentikasi)

        // 2. Act: Kirim request GET ke /api/user/preferences tanpa token
        $response = $this->getJson('/api/user/preferences');

        // 3. Assert: Verifikasi status 401 Unauthorized
        $response->assertUnauthorized();
    }

    #[Test]
    public function authenticated_user_can_get_their_preferences_when_they_have_none(): void
    {
        // 1. Arrange: Buat pengguna dan tokennya
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // 2. Act: Kirim request GET ke /api/user/preferences dengan token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user/preferences');

        // 3. Assert: Verifikasi response
        $response->assertStatus(200)
                 ->assertJsonStructure(['data']) // Struktur dasar untuk koleksi resource
                 ->assertJsonCount(0, 'data');   // Memastikan array 'data' kosong
    }

    #[Test]
    public function authenticated_user_can_get_their_existing_preferences(): void
    {
        // 1. Arrange: Buat pengguna, beberapa topik, dan set preferensi untuk pengguna
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $topic1 = Topic::factory()->create(['name' => 'Laravel']);
        $topic2 = Topic::factory()->create(['name' => 'VueJS']);
        $topic3 = Topic::factory()->create(['name' => 'Unrelated Topic']); // Topik yang tidak dipilih

        // Menggunakan relasi many-to-many untuk menetapkan preferensi
        // UserPreferenceController@index menggunakan $request->user()->topics()->orderBy('name')->get();
        $user->topics()->attach([$topic1->id, $topic2->id]);

        // 2. Act: Kirim request GET ke /api/user/preferences
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user/preferences');
        
        // 3. Assert: Verifikasi response
        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data') // Mengharapkan 2 preferensi topik
                 ->assertJsonPath('data.0.name', $topic1->name) // Verifikasi urutan berdasarkan nama (jika controller mengurutkan)
                 ->assertJsonPath('data.1.name', $topic2->name)
                 ->assertJsonMissing(['name' => $topic3->name]); // Pastikan topik yang tidak dipilih tidak ada
    }
    #[Test]
    public function authenticated_user_can_store_new_preferences(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $topic1 = Topic::factory()->create();
        $topic2 = Topic::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/preferences', [
            'topic_ids' => [$topic1->id, $topic2->id],
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'preferences' => [
                         '*' => ['id', 'name', 'slug', 'description']
                     ]
                 ])
                 ->assertJsonCount(2, 'preferences')
                 ->assertJsonPath('message', 'Preferensi belajar berhasil diperbarui.');
        
        $this->assertDatabaseHas('topic_user', ['user_id' => $user->id, 'topic_id' => $topic1->id]);
        $this->assertDatabaseHas('topic_user', ['user_id' => $user->id, 'topic_id' => $topic2->id]);
    }

    #[Test]
    public function authenticated_user_can_update_existing_preferences(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $topicOld1 = Topic::factory()->create();
        $topicOld2 = Topic::factory()->create();
        $user->topics()->attach([$topicOld1->id, $topicOld2->id]); // Preferensi awal

        $topicNew1 = Topic::factory()->create();
        $topicNew2 = Topic::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/preferences', [
            'topic_ids' => [$topicNew1->id, $topicNew2->id], // Preferensi baru
        ]);

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'preferences');
        
        $this->assertDatabaseMissing('topic_user', ['user_id' => $user->id, 'topic_id' => $topicOld1->id]);
        $this->assertDatabaseMissing('topic_user', ['user_id' => $user->id, 'topic_id' => $topicOld2->id]);
        $this->assertDatabaseHas('topic_user', ['user_id' => $user->id, 'topic_id' => $topicNew1->id]);
        $this->assertDatabaseHas('topic_user', ['user_id' => $user->id, 'topic_id' => $topicNew2->id]);
    }

    #[Test]
    public function authenticated_user_can_clear_all_preferences(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $topic1 = Topic::factory()->create();
        $user->topics()->attach($topic1->id); // Ada preferensi awal

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/preferences', [
            'topic_ids' => [], // Mengirim array kosong untuk menghapus semua
        ]);

        $response->assertStatus(200)
                 ->assertJsonCount(0, 'preferences');
        
        $this->assertDatabaseMissing('topic_user', ['user_id' => $user->id, 'topic_id' => $topic1->id]);
        $this->assertEmpty($user->fresh()->topics, "User topics should be empty after clearing preferences.");
    }

    #[Test]
    public function storing_preferences_fails_if_topic_id_is_invalid(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $validTopicId = Topic::factory()->create()->id;
        $invalidTopicId = 999; // ID yang tidak ada di database

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/preferences', [
            'topic_ids' => [$validTopicId, $invalidTopicId],
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['topic_ids.1']); // Error pada topic_ids indeks ke-1
    }

    #[Test]
    public function storing_preferences_fails_if_topic_ids_is_not_an_array(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/preferences', [
            'topic_ids' => 'bukan_array', // Data tidak valid
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['topic_ids']);
    }

    #[Test]
    public function unauthenticated_user_cannot_store_preferences(): void
    {
        $topic = Topic::factory()->create();
        $response = $this->postJson('/api/user/preferences', [
            'topic_ids' => [$topic->id],
        ]);

        $response->assertUnauthorized();
    }
}