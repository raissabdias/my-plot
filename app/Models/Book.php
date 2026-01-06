<?php

namespace App\Models;

use App\Enums\BookStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'review'
    ];

    protected $casts = [
        'status' => BookStatus::class,
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
}