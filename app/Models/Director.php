<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Director extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'age',
        'history',
        'nationality'
    ];

    // Relationship with movies
    public function movies()
    {
        return $this->hasMany(Movie::class);
    }
}
