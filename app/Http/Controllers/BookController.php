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
    public function index()
    {
        return Book::orderBy('created_at', 'desc')->get();
    }

    /**
     * Store a new book (POST /api/books)
     */
    public function store(StoreBookRequest $request)
    {
        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'status' => BookStatus::PLANNING,
            'image_url' => $request->image_url,
            'user_id' => 1
        ]);

        return response()->json($book, 201);
    }

    /**
     * Search for a book using Google Books API (GET /api/books/search?q=...)
     */
    public function search(Request $request, GoogleBooksService $googleBooksService)
    {
        $query = $request->query('q');
        if (!$query) {
            return response()->json(['error' => 'Query param is required'], 400);
        }

        $results = $googleBooksService->search($query);

        return response()->json($results);
    }

    /**
     * Update an existing book (PUT /api/books/{book})
     */
    public function update(StoreBookRequest $request, Book $book)
    {
        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'image_url' => $request->image_url,
            'status' => $request->status,
            'rating' => $request->rating,
        ]);

        return response()->json($book);
    }
}