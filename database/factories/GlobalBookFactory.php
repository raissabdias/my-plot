<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GlobalBookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'google_book_id' => $this->faker->unique()->isbn10(),
            'title' => $this->faker->sentence(3),
            'authors' => [$this->faker->name],
            'isbn' => $this->faker->isbn13(),
            'page_count' => $this->faker->numberBetween(100, 900),
            'cover_url' => $this->faker->imageUrl(),
        ];
    }
}