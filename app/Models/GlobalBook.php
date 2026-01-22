<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use App\Enums\BookStatus;

class GlobalBook extends Model
{
    use HasFactory;

    protected $table = 'global_books';

    protected $fillable = [
        'google_book_id',
        'title',
        'authors',
        'description',
        'cover_url',
        'isbn',
        'page_count',
        'published_date',
    ];

    protected $casts = [
        'authors' => 'array',
    ];

    /**
     * Scope a query to only include read books
     */
    #[Scope]
    protected function read(Builder $query): void
    {
        $query->where('book_user.status', BookStatus::READ->value);
    }

    /**
     * Scope a query to only include reading books
     */
    #[Scope]
    protected function reading(Builder $query): void
    {
        $query->where('book_user.status', BookStatus::READING->value);
    }

    /**
     * Scope a query to only include planning to read books
     */
    #[Scope]
    protected function planning(Builder $query): void
    {
        $query->where('book_user.status', BookStatus::PLANNING->value);
    }
}