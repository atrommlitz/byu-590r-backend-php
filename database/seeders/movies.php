<?php

namespace Database\Seeders;

use App\Models\movie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class movies extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = [
            [
                'title' => 'Inception',
                'year' => 2013,
                'genre' => 'Action',
                'file' => '/images/inception.jpeg',
                'movie_length' => 200.00 // Corrected key name to avoid spaces
            ],
            [
                'title' => 'Top Gun: Maverick',
                'year' => 2022,
                'genre' => 'Action',
                'file' => '/images/top_gun_maverick.jpeg',
                'movie_length' => 200.00
            ],
            [
                'title' => 'Nacho Libre',
                'year' => 2004,
                'genre' => 'Action',
                'file' => '/images/nacho_libre.jpeg',
                'movie_length' => 200.00
            ],
            [
                'title' => 'Interstellar',
                'year' => 2014,
                'genre' => 'Action',
                'file' => '/images/interstellar.jpeg',
                'movie_length' => 200.00
            ],
            [
                'title' => 'The Dark Knight',
                'year' => 2002,
                'genre' => 'Action',
                'file' => '/images/the_dark_knight.jpeg',
                'movie_length' => 200.00
            ]
        ];
        movie::insert($movies);
    }
}
