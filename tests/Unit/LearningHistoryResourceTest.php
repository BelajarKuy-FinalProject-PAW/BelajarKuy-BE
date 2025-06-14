<?php

namespace Tests\Unit;

use App\Http\Resources\LearningHistoryResource; // Import resource Anda
use App\Models\LearningHistory;
use App\Models\Material;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase; // Untuk menggunakan factory dan database
use Illuminate\Http\Request;
use Tests\TestCase; // Menggunakan TestCase Laravel
use PHPUnit\Framework\Attributes\Test;

class LearningHistoryResourceTest extends TestCase // Pastikan extends Tests\TestCase
{
    use RefreshDatabase; // Penting karena kita akan menggunakan factory dan relasi

    #[Test]
    public function it_transforms_learning_history_correctly_with_completed_at_and_material(): void
    {
        // 1. Arrange: Buat User, Material, dan LearningHistory menggunakan factory
        $user = User::factory()->create();
        $material = Material::factory()->create(['title' => 'Judul Materi Test']);
        
        $now = Carbon::now();
        $completedTime = Carbon::yesterday();

        $learningHistory = LearningHistory::factory()->for($user)->for($material)->create([
            'completed_at' => $completedTime,
            'created_at' => $now, // Untuk mengontrol 'recorded_at'
            'updated_at' => $now,
        ]);

        // Buat mock request sederhana
        $request = Request::create('/');

        // Buat instance LearningHistoryResource
        // Eager load relasi 'material' untuk memastikan resource bisa mengaksesnya
        // Meskipun factory->for() biasanya sudah mengaturnya, ini praktik yang baik.
        $learningHistoryResource = new LearningHistoryResource($learningHistory->load('material'));

        // 2. Act: Transformasi resource menjadi array
        $resourceArray = $learningHistoryResource->toArray($request);

        // 3. Assert: Verifikasi struktur dan nilai
        $expectedArray = [
            'id' => $learningHistory->id,
            'material' => [
                'id' => $material->id,
                'title' => $material->title,
            ],
            'completed_at' => $completedTime->toIso8601String(),
            'recorded_at' => $now->toIso8601String(),
        ];
        
        $this->assertEquals($expectedArray, $resourceArray);
    }

    #[Test]
    public function it_transforms_learning_history_correctly_when_completed_at_is_null(): void
    {
        // 1. Arrange
        $user = User::factory()->create();
        $material = Material::factory()->create(['title' => 'Judul Materi Lain']);
        $now = Carbon::now();

        $learningHistory = LearningHistory::factory()->for($user)->for($material)->create([
            'completed_at' => null, // completed_at sengaja null
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $request = Request::create('/');
        $learningHistoryResource = new LearningHistoryResource($learningHistory->load('material'));

        // 2. Act
        $resourceArray = $learningHistoryResource->toArray($request);

        // 3. Assert
        $expectedArray = [
            'id' => $learningHistory->id,
            'material' => [
                'id' => $material->id,
                'title' => $material->title,
            ],
            'completed_at' => null, // Diharapkan null
            'recorded_at' => $now->toIso8601String(),
        ];
        
        $this->assertEquals($expectedArray, $resourceArray);
    }
}