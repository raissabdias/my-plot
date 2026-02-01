<?php

namespace App\Http\Controllers;

use App\Enums\BookStatus;
use App\Models\GlobalBook;
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
        $lang = $request->query('langRestrict');
        if (!$query) {
            return response()->json(['error' => 'Query param is required'], 400);
        }

        $results = $googleBooksService->search($query, $lang);

        return response()->json($results);
    }

    /**
     * Get detailed information about a book by its Google Book ID (GET /api/book/{id})
     */
    public function show($id, Request $request, GoogleBooksService $googleBooksService)
    {
        $bookDetails = $googleBooksService->getBookDetails($id);

        if (!$bookDetails) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        $userStatus = null;
        
        $user = $request->user('sanctum');
        $globalBook = GlobalBook::where('google_book_id', $id)->first();

        # Retrieve user's status for this book if logged in
        if ($user && $globalBook) {
            $userBook = $user->bookshelf()->where('global_book_id', $globalBook->id)->first();
            if ($userBook) {
                $userStatus = $userBook->pivot->status_formatted;
            }
        }
        $bookDetails['user_status'] = $userStatus;

        # Retrieve community reviews for this book
        $communityReviews = [];
        if ($globalBook) {
            $reviews = $globalBook->users()
                ->wherePivotNotNull('review')
                ->wherePivot('review', '!=', '')
                ->wherePivot('status', BookStatus::READ->value)
                ->withPivot(['review', 'rating', 'updated_at', 'status'])
                ->latest('book_user.updated_at')
                ->take(10)
                ->get();

            $communityReviews = $reviews->map(function ($reviewer) {
                return [
                    'id' => $reviewer->id,
                    'name' => $reviewer->name,
                    'avatar' => $reviewer->avatar,
                    'rating' => $reviewer->pivot->rating,
                    'review' => $reviewer->pivot->review,
                    'status' => $reviewer->pivot->status,
                    'date' => $reviewer->pivot->updated_at->diffForHumans(),
                ];
            });
        }
        $bookDetails['community_reviews'] = $communityReviews;

        return response()->json($bookDetails);
    }
}