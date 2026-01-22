<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\BookStatus;

class ShelfStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'google_book_id' => 'required|string',
            'title' => 'required|string',
            'authors' => 'nullable',
            'isbn' => 'nullable|string',
            'image_url' => 'nullable|url',
            'page_count' => 'nullable|integer',
            'status' => ['required', Rule::enum(BookStatus::class)],
            'rating' => 'nullable|integer|min:0|max:5',
            'review' => 'nullable|string|max:2000',
            'started_reading_at' => 'nullable|date',
            'finished_reading_at' => 'nullable|date|after_or_equal:started_reading_at',
        ];
    }
}