<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\GlobalBook;
use App\Enums\BookStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShelfTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_add_a_book_to_their_shelf()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        # Book data (Simulating the Google Books payload)
        $payload = [
            'google_book_id' => '123456',
            'title' => 'Dom Casmurro',
            'authors' => ['Machado de Assis'],
            'status' => BookStatus::READING->value,
            'started_reading_at' => '2024-01-01',
        ];

        $response = $this->postJson('/api/shelf', $payload);

        # Assert response status
        $response->assertStatus(201);

        # Check if the global book was created
        $this->assertDatabaseHas('global_books', [
            'google_book_id' => '123456',
            'title' => 'Dom Casmurro'
        ]);

        # Check if the book was linked to the user's shelf
        $book = GlobalBook::where('google_book_id', '123456')->first();
        
        $this->assertDatabaseHas('book_user', [
            'user_id' => $user->id,
            'global_book_id' => $book->id,
            'status' => 'reading',
            'started_at' => '2024-01-01 00:00:00'
        ]);
    }

    #[Test]
    public function user_can_update_reading_status()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        # Create a book and link it to the user
        $book = GlobalBook::factory()->create();
        $user->bookshelf()->attach($book->id, ['status' => 'reading']);

        # Update payload
        $payload = [
            'status' => 'read',
            'rating' => 5,
            'review' => 'Obra prima!',
            'finished_reading_at' => '2024-02-01'
        ];

        $response = $this->putJson("/api/shelf/{$book->id}", $payload);

        # Assert response status
        $response->assertStatus(200);

        # Check if the book_user pivot table was updated
        $this->assertDatabaseHas('book_user', [
            'user_id' => $user->id,
            'global_book_id' => $book->id,
            'status' => 'read',
            'rating' => 5,
            'review' => 'Obra prima!'
        ]);
    }

    #[Test]
    public function user_cannot_update_book_not_in_shelf()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        # Book exists but belongs to ANOTHER user
        $book = GlobalBook::factory()->create();
        $otherUser->bookshelf()->attach($book->id, ['status' => 'reading']);

        $payload = ['status' => 'read'];

        # Attempt to update
        $response = $this->putJson("/api/shelf/{$book->id}", $payload);

        # Should fail (403 Forbidden or 404 Not Found, depending on your validation)
        $response->assertStatus(403); 
    }

    #[Test]
    public function user_can_remove_book_from_shelf()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $book = GlobalBook::factory()->create();
        $user->bookshelf()->attach($book->id, ['status' => 'planning_to_read']);

        $response = $this->deleteJson("/api/shelf/{$book->id}");

        $response->assertStatus(200);

        # The relationship should be gone
        $this->assertDatabaseMissing('book_user', [
            'user_id' => $user->id,
            'global_book_id' => $book->id
        ]);

        # The global book should still exist
        $this->assertDatabaseHas('global_books', ['id' => $book->id]);
    }
}