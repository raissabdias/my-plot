<?php

namespace App\Models;

use App\Enums\BookStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'author',
        'image_url',
        'isbn',
        'status',
        'rating',
        'review',
        'started_reading_at',
        'finished_reading_at',
    ];

    protected $casts = [
        'status' => BookStatus::class,
        'started_reading_at' => 'datetime',
        'finished_reading_at' => 'datetime',
    ];

    protected $appends = [
        'status_formatted'
    ];

    /**
     * Get the user that owns the book
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the formatted status attribute
     * @return array
     */
    public function getStatusFormattedAttribute()
    {
        return [
            'label' => $this->status->label(),
            'color' => $this->status->color(),
            'icon' => $this->status->icon(),
        ];
    }

    /**
     * Scope a query to only include read books
     */
    #[Scope]
    protected function read(Builder $query): void
    {
        $query->where('status', BookStatus::READ->value);
    }

    /**
     * Scope a query to only include reading books
     */
    #[Scope]
    protected function reading(Builder $query): void
    {
        $query->where('status', BookStatus::READING->value);
    }

    /**
     * Scope a query to only include planning to read books
     */
    #[Scope]
    protected function planning(Builder $query): void
    {
        $query->where('status', BookStatus::PLANNING->value);
    }
}