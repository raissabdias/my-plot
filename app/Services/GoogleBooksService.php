<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleBooksService
{
    /**
     * Search for books using the Google Books API.
     */
    public function search(string $query): array
    {
        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $query,
            'maxResults' => 10
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
                'title' => $info['title'] ?? 'Sem Título',
                'author' => $info['authors'][0] ?? 'Desconhecido',
                'isbn' => $isbn,
                'image_url' => isset($info['imageLinks']['thumbnail']) 
                    ? str_replace('http://', 'https://', $info['imageLinks']['thumbnail']) 
                    : null,
            ];
        })->toArray();
    }
}