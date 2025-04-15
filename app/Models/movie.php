<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'year',
        'genre',
        'file',
        'movie_length',
        'director_id',
        'rating_id'
    ];

    public function director()
    {
        return $this->belongsTo(Director::class);
    }

    public function rating()
    {
        return $this->belongsTo(Rating::class);
    }
}
