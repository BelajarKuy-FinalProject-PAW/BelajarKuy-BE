<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Topic>
 */
class TopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array{
    $name = $this->faker->unique()->words(3, true); // Nama topik unik
    return [
        'name' => $name,
        'slug' => Str::slug($name), // Model Anda akan auto-generate ini, tapi baik untuk konsistensi
        'description' => $this->faker->sentence,
        ];
    }
}
