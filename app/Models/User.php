<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Current year reading goal
     */
    public function currentYearGoal()
    {
        return $this->hasOne(ReadingGoal::class)->where('year', date('Y'));
    }

    /**
     * The global books in the user's bookshelf
     */
    public function bookshelf()
    {
        return $this->belongsToMany(GlobalBook::class, 'book_user')
                    ->using(BookUser::class) // Já vamos criar esse arquivo abaixo
                    ->withPivot('status', 'review', 'rating', 'started_at', 'finished_at')
                    ->withTimestamps();
    }
}
