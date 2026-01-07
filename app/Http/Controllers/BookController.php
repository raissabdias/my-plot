<?php

namespace App\Http\Controllers;

use App\Enums\BookStatus;
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
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
        ]);

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
}