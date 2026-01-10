<?php

namespace App\Http\Controllers;

use App\Enums\BookStatus;
use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Services\GoogleBooksService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * List all books (GET /api/books)
     */
    public function index(Request $request)
    {
        return $request->user()->books()->latest()->get();
    }

    /**
     * Store a new book (POST /api/books)
     */
    public function store(StoreBookRequest $request)
    {
        $book = $request->user()->books()->create([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'status' => $request->status,
            'rating' => $request->rating,
            'image_url' => $request->image_url,
            'review' => $request->review
        ]);

        return response()->json($book, 201);
    }

    /**
     * Search for a book using Google Books API (GET /api/books/search?q=...)
     */
    public function search(Request $request, GoogleBooksService $googleBooksService)
    {
        $query = $request->query('query');
        if (!$query) {
            return response()->json(['error' => 'Query param is required'], 400);
        }

        $results = $googleBooksService->search($query);

        return response()->json($results);
    }

    /**
     * Update an existing book (PUT /api/books/{book})
     */
    public function update(StoreBookRequest $request, string $id)
    {
        $book = $request->user()->books()->findOrFail($id);
        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'image_url' => $request->image_url,
            'status' => $request->status,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return response()->json($book);
    }

    /**
     * Delete a book (DELETE /api/books/{book})
     */
    public function destroy(Request $request, string $id)
    {
        $book = $request->user()->books()->findOrFail($id);
        $book->delete();

        return response()->noContent();
    }
}