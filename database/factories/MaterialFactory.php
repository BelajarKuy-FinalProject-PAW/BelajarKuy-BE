<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'title' => $this->faker->sentence,
        'description' => $this->faker->paragraph,
        'topic_id' => \App\Models\Topic::factory(),        // Jika tidak ada TopicFactory atau tidak ingin dependensi, bisa di-set saat create di test
    ];
}
}
