<?php

namespace App\Http\Controllers;

use App\Enums\BookStatus;
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
    public function search(Request $request)
    {
        $query = $request->query('q');
        if (!$query) {
            return response()->json(['error' => 'Query param is required'], 400);
        }

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $query,
            'maxResults' => 10
        ]);

        $data = $response->json();
        if (empty($data['items'])) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        $results = collect($data['items'])->map(function ($item) {
            $info = $item['volumeInfo'];
            $isbn = $info['industryIdentifiers'][0]['identifier'] ?? null;

            return [
                'title' => $info['title'] ?? 'Sem Título',
                'author' => $info['authors'][0] ?? 'Desconhecido',
                'isbn' => $isbn,
                'image_url' => isset($info['imageLinks']['thumbnail']) 
                    ? str_replace('http://', 'https://', $info['imageLinks']['thumbnail']) 
                    : null,
            ];
        });

        return response()->json($results);
    }
}