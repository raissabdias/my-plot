<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\BookStatus;
use Illuminate\Validation\Rules\Enum;

class ShelfStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        # Check if the field is missing or null in the request
        if (!$this->has('status')) {
            $this->merge([
                'status' => BookStatus::PLANNING->value
            ]);
        }
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
            'started_at' => 'nullable|date',
            'finished_at' => 'nullable|date|after_or_equal:started_at',
            'is_public' => 'nullable|boolean',
        ];
    }
}