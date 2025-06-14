<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LearningHistory>
 */
class LearningHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    // database/factories/LearningHistoryFactory.php
    public function definition(): array
    {
    return [
        'user_id' => \App\Models\User::factory(),
        'material_id' => \App\Models\Material::factory(),
        'completed_at' => $this->faker->optional(0.7, null)->dateTimeThisYear(),
    ];
    }
}
