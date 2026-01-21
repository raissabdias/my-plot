<?php

namespace App\Models;

use App\Enums\BookStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BookUser extends Pivot
{
    public $incrementing = true;

    protected $table = 'book_user';

    protected $fillable = [
        'user_id',
        'global_book_id',
        'status',
        'review',
        'rating',
        'started_at',
        'finished_at'
    ];

    protected $casts = [
        'status' => BookStatus::class,
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    protected $appends = [
        'status_formatted'
    ];

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