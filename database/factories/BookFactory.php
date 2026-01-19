<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'author' => fake()->name(),
            'image_url' => fake()->imageUrl(),
            'isbn' => fake()->isbn13(),
            'status' => 'planning_to_read',
            'user_id' => \App\Models\User::factory(), // Cria um user automático se não passar um
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
