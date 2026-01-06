<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
            'status' => 'planning_to_read',
            'image_url' => $request->image_url,
            'user_id' => 1
        ]);

        return response()->json($book, 201);
    }

    /**
     * Search for a book using Google Books API (GET /api/books/search?q=...)
     */
    public function search(Request $request)
    {
        $query = $request->query('q');
        if (!$query) {
            return response()->json(['error' => 'Query param is required'], 400);
        }

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $query,
            'maxResults' => 1 # TODO: list more results
        ]);

        $data = $response->json();
        if (empty($data['items'])) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        $bookData = $data['items'][0]['volumeInfo'];

        return response()->json([
            'title' => $bookData['title'] ?? '',
            'author' => $bookData['authors'][0] ?? 'Unknown Author',
            'isbn' => $bookData['industryIdentifiers'][0]['identifier'] ?? null,
            'image_url' => isset($bookData['imageLinks']['thumbnail']) 
                ? str_replace('http://', 'https://', $bookData['imageLinks']['thumbnail']) 
                : null,
            'description' => $bookData['description'] ?? '',
        ]);
    }
}