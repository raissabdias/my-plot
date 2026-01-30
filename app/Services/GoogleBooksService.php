<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

use function Illuminate\Log\log;

class GoogleBooksService
{
    protected $apiKey;
    
    public function __construct()
    {
        $this->apiKey = env('GOOGLE_BOOKS_API_KEY');
    }

    /**
     * Search for books using the Google Books API
     */
    public function search(string $query, $lang = null): array
    {
        # Cache results for 1 hour
        return Cache::remember("search_{$query}", 3600, function () use ($query, $lang) {
            $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
                'q' => $query,
                'maxResults' => 10,
                'key' => $this->apiKey,
                'langRestrict' => $lang,
                'printType' => 'books',
                'orderBy' => 'relevance',
            ]);

            $data = $response->json();

            if (empty($data['items'])) {
                return [];
            }

            # Format the results
            return collect($data['items'])->map(function ($item) {
                $info = $item['volumeInfo'];
                $isbn = $info['industryIdentifiers'][0]['identifier'] ?? null;

                return [
                    'id' => $item['id'],
                    'title' => $info['title'] ?? 'Sem Título',
                    'author' => $info['authors'][0] ?? 'Desconhecido',
                    'isbn' => $isbn,
                    'page_count' => $info['pageCount'] ?? null,
                    'image_url' => isset($info['imageLinks']['thumbnail']) 
                        ? str_replace('http://', 'https://', $info['imageLinks']['thumbnail']) 
                        : null,
                ];
            })->toArray();
        });
    }

    /**
     * Get detailed information about a book by its Google Book ID
     */
    public function getBookDetails(string $googleBookId): ?array
    {
        # Cache results for 7 days
        return Cache::remember("book_details_{$googleBookId}", 60 * 60 * 24 * 7, function () use ($googleBookId) {
            $response = Http::get("https://www.googleapis.com/books/v1/volumes/{$googleBookId}", [
                'key' => $this->apiKey
            ]);

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            if (empty($data['volumeInfo'])) {
                return null;
            }

            $info = $data['volumeInfo'];
            $isbn = $info['industryIdentifiers'][0]['identifier'] ?? null;

            # Format the results
            return [
                'id' => $data['id'],
                'title' => $info['title'] ?? 'Sem Título',
                'authors' => $info['authors'] ?? ['Desconhecido'],
                'description' => $info['description'] ?? '',
                'publisher' => $info['publisher'] ?? '',
                'published_date' => $info['publishedDate'] ?? '',
                'page_count' => $info['pageCount'] ?? null,
                'categories' => $info['categories'] ?? [],
                'average_rating' => $info['averageRating'] ?? null,
                'ratings_count' => $info['ratingsCount'] ?? null,
                'isbn' => $isbn,
                'image_url' => isset($info['imageLinks']['thumbnail']) 
                    ? str_replace('http://', 'https://', $info['imageLinks']['thumbnail']) 
                    : null,
            ];
        });
    }
}