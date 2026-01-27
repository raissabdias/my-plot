<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\BookStatus;
use Illuminate\Support\Facades\Log;

class ShelfUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $bookId = $this->route('shelf');
        
        return $this->user()->bookshelf()->where('global_book_id', $bookId)->exists();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(BookStatus::class)],
            'rating' => 'nullable|integer|min:0|max:5',
            'review' => 'nullable|string|max:2000',
            'started_at'  => 'nullable|date',
            'finished_at' => 'nullable|date|after_or_equal:started_at',
        ];
    }
}