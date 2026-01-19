<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\ReadingGoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingGoalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A test to verify that the reading goal progress is calculated correctly
     */
    public function test_it_calculates_reading_goal_progress_correctly()
    {
        # Create user
        $user = User::factory()->create();

        # Define a reading goal for the user
        ReadingGoal::create([
            'user_id' => $user->id,
            'year' => date('Y'),
            'target' => 10
        ]);

        # 5 books marked as 'read' for that user
        Book::factory()->count(5)->create([
            'user_id' => $user->id,
            'status' => 'read',
            'finished_reading_at' => now(),
        ]);

        # (Optional) Create a 'reading' book to ensure it does NOT count towards the goal
        Book::factory()->create([
            'user_id' => $user->id,
            'status' => 'reading'
        ]);

        $response = $this->actingAs($user)->getJson('/api/dashboard');

        $response
            ->assertStatus(200)
            ->assertJson([
                'goal' => [
                    'target' => 10,
                    'read' => 5,
                    'percentage' => 50,
                ]
            ]);
    }
}
