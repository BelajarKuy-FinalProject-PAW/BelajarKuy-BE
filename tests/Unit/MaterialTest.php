<?php

namespace Tests\Unit;

use App\Models\Material;
use App\Models\Topic; // Digunakan untuk relasi topic()
use App\Models\LearningHistory; // Digunakan untuk relasi learningHistories()
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Tipe relasi yang diharapkan
use Illuminate\Database\Eloquent\Relations\HasMany; // Tipe relasi yang diharapkan
use Tests\TestCase; // Menggunakan TestCase Laravel untuk model Eloquent
// use Illuminate\Foundation\Testing\RefreshDatabase; // Opsional untuk test ini
use PHPUnit\Framework\Attributes\Test;

class MaterialTest extends TestCase // Pastikan extends Tests\TestCase
{
    // use RefreshDatabase; // Bisa diaktifkan jika Anda menggunakan factory untuk membuat instance

    #[Test]
    public function material_belongs_to_a_topic(): void
    {
        // 1. Arrange: Buat instance dari Material model.
        $material = new Material(); 

        // 2. Act: Panggil method relasi topic().
        $relation = $material->topic();

        // 3. Assert: Pastikan method mengembalikan instance dari BelongsTo.
        $this->assertInstanceOf(BelongsTo::class, $relation, "Relasi 'topic' seharusnya adalah instance dari BelongsTo.");
        
        // Opsional: Verifikasi foreign key jika diperlukan
        // $this->assertEquals('topic_id', $relation->getForeignKeyName());
    }

    #[Test]
    public function material_has_many_learning_histories(): void
    {
        // 1. Arrange
        $material = new Material();

        // 2. Act
        $relation = $material->learningHistories();

        // 3. Assert
        $this->assertInstanceOf(HasMany::class, $relation, "Relasi 'learningHistories' seharusnya adalah instance dari HasMany.");

        // Opsional: Verifikasi foreign key dan local key jika diperlukan
        // $this->assertEquals('material_id', $relation->getForeignKeyName());
        // $this->assertEquals('id', $relation->getLocalKeyName());
    }
}