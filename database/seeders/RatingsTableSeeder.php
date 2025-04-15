<?php

namespace Database\Seeders;

use App\Models\Rating;
use Illuminate\Database\Seeder;

class RatingsTableSeeder extends Seeder
{
    public function run()
    {
        $ratings = [
            ['name' => 'G', 'description' => 'General Audience'],
            ['name' => 'PG', 'description' => 'Parental Guidance Suggested'],
            ['name' => 'PG-13', 'description' => 'Parental Guidance for Children Under 13'],
            ['name' => 'R', 'description' => 'Restricted'],
        ];

        foreach ($ratings as $rating) {
            Rating::create($rating);
        }
    }
}