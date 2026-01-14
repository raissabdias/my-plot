<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; # TODO: Implement authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string', 
            'image_url' => 'nullable|url', 
            'review' => 'nullable|string', 
            'status' => 'required|in:read,reading,planning_to_read',
            'rating' => 'nullable|integer|min:0|max:5',
            'started_reading_at' => 'nullable|date',
            'finished_reading_at' => 'nullable|date|after_or_equal:started_reading_at'
        ];
    }
}
