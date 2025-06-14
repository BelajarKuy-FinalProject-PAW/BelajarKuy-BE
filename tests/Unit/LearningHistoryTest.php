<?php

namespace Tests\Unit;

use App\Models\LearningHistory;
use App\Models\User;        // Digunakan untuk relasi user()
use App\Models\Material;    // Digunakan untuk relasi material()
use Carbon\Carbon;          // Untuk bekerja dengan instance datetime
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Tipe relasi yang diharapkan
use Tests\TestCase;         // Menggunakan TestCase Laravel untuk model Eloquent
// use Illuminate\Foundation\Testing\RefreshDatabase; // Opsional untuk test ini
use PHPUnit\Framework\Attributes\Test;

class LearningHistoryTest extends TestCase // Pastikan extends Tests\TestCase
{
    // use RefreshDatabase; // Bisa diaktifkan jika Anda menggunakan factory

    #[Test]
    public function completed_at_is_casted_to_datetime(): void
    {
        // 1. Arrange: Buat instance LearningHistory dengan tanggal dalam format string
        $learningHistory = new LearningHistory([
            'completed_at' => '2025-01-15 10:30:00',
        ]);

        // 2. Act: Akses atribut completed_at (casting akan terjadi di sini)
        $completedAt = $learningHistory->completed_at;

        // 3. Assert: Pastikan atribut tersebut adalah instance dari Carbon (atau DateTimeImmutable)
        // Di Laravel, cast 'datetime' biasanya menghasilkan objek Carbon.
        $this->assertInstanceOf(Carbon::class, $completedAt, "Kolom 'completed_at' seharusnya di-cast ke Carbon/DateTime.");
        
        // Opsional: Verifikasi nilai tanggal dan waktu jika perlu
        $this->assertEquals(2025, $completedAt->year);
        $this->assertEquals(1, $completedAt->month);
        $this->assertEquals(15, $completedAt->day);
    }

    #[Test]
    public function completed_at_is_null_if_not_set(): void
    {
        // 1. Arrange: Buat instance LearningHistory tanpa completed_at
        $learningHistory = new LearningHistory();

        // 2. Act: Akses atribut completed_at
        $completedAt = $learningHistory->completed_at;
        
        // 3. Assert: Pastikan atribut tersebut null karena tidak di-set dan nullable di DB
        $this->assertNull($completedAt, "Kolom 'completed_at' seharusnya null jika tidak di-set.");
    }

    #[Test]
    public function learning_history_belongs_to_a_user(): void
    {
        // 1. Arrange
        $learningHistory = new LearningHistory();

        // 2. Act
        $relation = $learningHistory->user();

        // 3. Assert
        $this->assertInstanceOf(BelongsTo::class, $relation, "Relasi 'user' seharusnya adalah instance dari BelongsTo.");
    }

    #[Test]
    public function learning_history_belongs_to_a_material(): void
    {
        // 1. Arrange
        $learningHistory = new LearningHistory();

        // 2. Act
        $relation = $learningHistory->material();

        // 3. Assert
        $this->assertInstanceOf(BelongsTo::class, $relation, "Relasi 'material' seharusnya adalah instance dari BelongsTo.");
    }
}