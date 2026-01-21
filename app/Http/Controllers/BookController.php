<?php

namespace App\Http\Controllers;

use App\Services\GoogleBooksService;
use Illuminate\Http\Request;

class BookController extends Controller
{
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
}