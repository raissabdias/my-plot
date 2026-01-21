<?php

namespace App\Http\Controllers;

use App\Models\GlobalBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShelfController extends Controller
{
    /**
     * Display a listing of the user's shelf books
     */
    public function index(Request $request)
    {
        $query = $request->user()->bookshelf()
            ->withPivot(['status', 'rating', 'review', 'started_at', 'finished_at'])
            ->orderByPivot('updated_at', 'desc');

        # Search
        if ($request->has('search') && $request->search) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('authors', 'like', $search);
            });
        }

        # Status filtering
        if ($request->has('status') && $request->status) {
            $query->wherePivot('status', $request->status);
        }

        # Pagination
        $perPage = $request->input('per_page', 10);
        $paginatedBooks = $query->paginate($perPage);

        # Formatting
        $paginatedBooks->through(function ($book) {
            return [
                'id' => $book->id,
                'google_book_id' => $book->google_book_id,
                'title' => $book->title,
                'author' => is_array($book->authors) ? implode(', ', $book->authors) : $book->authors,
                'image_url' => $book->cover_url,
                'isbn' => $book->isbn,
                'page_count' => $book->page_count,
                'status' => $book->pivot->status,
                'rating' => $book->pivot->rating,
                'review' => $book->pivot->review,
                'started_at' => $book->pivot->started_at,
                'finished_at' => $book->pivot->finished_at,
                'status_formatted' => $book->pivot->status_formatted,
            ];
        });

        return response()->json($paginatedBooks);
    }

    /**
     * Store a book in the user's shelf
     */
    public function store(Request $request)
    {
        # TODO: refactor validation to a FormRequest
        $validated = $request->validate([
            'google_book_id' => 'required|string',
            'title' => 'required|string',
            'authors' => 'nullable',
            'isbn' => 'nullable|string',
            'image_url' => 'nullable|string',
            'page_count' => 'nullable|integer',
            'status' => 'required|string',
            'rating' => 'nullable|integer',
            'review' => 'nullable|string',
            'started_at' => 'nullable|date',
            'finished_at' => 'nullable|date',
        ]);

        return DB::transaction(function () use ($request) {

            # Try to find the book in the global_books table
            $book = GlobalBook::firstOrCreate(
                ['google_book_id' => $request->google_book_id],
                [
                    'title' => $request->title,
                    'authors' => $request->authors,
                    'isbn' => $request->isbn,
                    'cover_url' => $request->image_url,
                    'page_count' => $request->page_count ?? 0,
                ]
            );

            # Attach the book to the user's bookshelf with pivot data
            $request->user()->bookshelf()->syncWithoutDetaching([
                $book->id => [
                    'status' => $request->status,
                    'rating' => $request->rating,
                    'review' => $request->review,
                    'started_at'  => $request->started_at,
                    'finished_at' => $request->finished_at,
                ]
            ]);

            return response()->json([
                'message' => 'Livro adicionado à estante global!',
                'book' => $book
            ], 201);
        });
    }

    /**
     * Update the specified book in the user's shelf
     */
    public function update(Request $request, $id)
    {
        # TODO: refactor validation to a FormRequest
        $validated = $request->validate([
            'status' => 'required|string',
            'rating' => 'nullable|integer',
            'review' => 'nullable|string',
            'started_at' => 'nullable|date',
            'finished_at' => 'nullable|date',
        ]);

        $request->user()->bookshelf()->updateExistingPivot($id, [
            'status' => $request->status,
            'rating' => $request->rating,
            'review' => $request->review,
            'started_at' => $request->started_at,
            'finished_at' => $request->finished_at,
        ]);

        return response()->json(['message' => 'Leitura atualizada com sucesso!']);
    }

    /**
     * Remove the specified book from the user's shelf
     */
    public function destroy(Request $request, $id)
    {
        $request->user()->bookshelf()->detach($id);

        return response()->json(['message' => 'Livro removido da estante.']);
    }
}
