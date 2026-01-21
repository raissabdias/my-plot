<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * The users that have this global book in their collection
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'book_user')
                    ->withPivot('status', 'review', 'rating', 'started_at', 'finished_at')
                    ->withTimestamps();
    }
}