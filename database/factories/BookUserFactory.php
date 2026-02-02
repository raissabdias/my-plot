<?php

namespace Database\Factories;

use App\Enums\BookStatus;
use App\Models\BookUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookUser>
 */
class BookUserFactory extends Factory
{
    protected $model = BookUser::class;

    public function definition(): array
    {
        $status = fake()->randomElement(BookStatus::cases());
        
        $startedAt = null;
        $finishedAt = null;

        if ($status === BookStatus::READING || $status === BookStatus::READ) {
            $startedAt = fake()->dateTimeBetween('-1 year', '-1 month');
        }

        if ($status === BookStatus::READ) {
            $finishedAt = fake()->dateTimeBetween($startedAt, 'now');
        }

        $hasReview = ($status === BookStatus::READ);

        return [
            'status' => $status,
            'rating' => $hasReview ? fake()->numberBetween(3, 5) : null,
            'review' => $hasReview ? fake()->paragraph(2) : null,
            'is_public' => fake()->boolean(90),
            'started_at' => $startedAt,
            'finished_at' => $finishedAt,
        ];
    }
}