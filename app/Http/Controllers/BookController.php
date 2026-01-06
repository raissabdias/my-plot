<?php

namespace App\Http\Controllers;

use App\Models\Book;
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
            'status' => 'planning_to_read',
            'user_id' => 1
        ]);

        return response()->json($book, 201);
    }
}