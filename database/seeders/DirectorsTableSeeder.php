<?php

namespace Database\Seeders;

use App\Models\Director;
use Illuminate\Database\Seeder;

class DirectorsTableSeeder extends Seeder
{
    public function run()
    {
        $directors = [
            [
                'full_name' => 'Christopher Nolan',
                'age' => 52,
                'history' => 'Known for complex narratives and innovative filmmaking. Directed Inception, The Dark Knight trilogy, and Interstellar.',
                'nationality' => 'British-American'
            ],
            [
                'full_name' => 'Joseph Kosinski',
                'age' => 48,
                'history' => 'Known for his visual effects expertise. Directed Top Gun: Maverick and Tron: Legacy.',
                'nationality' => 'American'
            ],
            [
                'full_name' => 'Jared Hess',
                'age' => 43,
                'history' => 'Independent filmmaker known for quirky comedies. Directed Napoleon Dynamite and Nacho Libre.',
                'nationality' => 'American'
            ]
        ];

        foreach ($directors as $director) {
            Director::create($director);
        }
    }
}
