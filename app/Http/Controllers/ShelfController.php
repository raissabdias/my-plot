<?php

namespace App\Http\Controllers;

use App\Models\GlobalBook;
use Illuminate\Http\Request;
use App\Http\Requests\ShelfStoreRequest;
use App\Http\Requests\ShelfUpdateRequest;

class ShelfController extends Controller
{
    /**
     * Display a listing of the user's shelf books
     */
    public function index(Request $request)
    {
        $query = $request->user()->bookshelf()
            ->withPivot(['status', 'rating', 'review', 'started_at', 'finished_at', 'is_public'])
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
                'is_public' => $book->pivot->is_public,
            ];
        });

        return response()->json($paginatedBooks);
    }

    /**
     * Store a book in the user's shelf
     */
    public function store(ShelfStoreRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        $book = GlobalBook::firstOrCreate(
            ['google_book_id' => $data['google_book_id']],
            [
                'title' => $data['title'],
                'authors' => $data['authors'],
                'isbn' => $data['isbn'] ?? null,
                'cover_url' => $data['image_url'] ?? null,
                'page_count' => $data['page_count'] ?? 0,
            ]
        );

        $user->bookshelf()->syncWithoutDetaching([
            $book->id => [
                'status' => $data['status'],
                'rating' => $data['rating'] ?? null,
                'review' => $data['review'] ?? null,
                'started_at' => $data['started_reading_at'] ?? null,
                'finished_at' => $data['finished_reading_at'] ?? null,
                'is_public' => $data['is_public'] ?? null
            ]
        ]);

        $book = $user->bookshelf()->where('global_book_id', $book->id)->first()->pivot;

        return response()->json(['message' => 'Livro adicionado à estante!', 'book' => $book], 201);
    }

    /**
     * Update the specified book in the user's shelf
     */
    public function update(ShelfUpdateRequest $request, $id)
    {
        $data = $request->validated();

        $request->user()->bookshelf()->updateExistingPivot($id, [
            'status' => $data['status'],
            'rating' => $data['rating'] ?? null,
            'review' => $data['review'] ?? null,
            'started_at' => $data['started_at'] ?? null,
            'finished_at' => $data['finished_at'] ?? null,
            'is_public' => $data['is_public'] ?? null,
        ]);

        return response()->json(['message' => 'Leitura atualizada com sucesso!']);
    }

    /**
     * Remove the specified book from the user's shelf
     */
    public function destroy(Request $request, $id)
    {
        $deleted = $request->user()->bookshelf()->detach($id);

        if (!$deleted) {
            return response()->json(['message' => 'Livro não encontrado na sua estante.'], 404);
        }

        return response()->json(['message' => 'Livro removido da estante.']);
    }
}
