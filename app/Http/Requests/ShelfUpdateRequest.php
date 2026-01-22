<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\BookStatus;

class ShelfUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        # TODO: Verificar se o usuário tem permissão para atualizar este livro na estante
        return true; 
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(BookStatus::class)],
            'rating' => 'nullable|integer|min:0|max:5',
            'review' => 'nullable|string|max:2000',
            'started_reading_at'  => 'nullable|date',
            'finished_reading_at' => 'nullable|date|after_or_equal:started_reading_at',
        ];
    }
}