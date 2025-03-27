<?php

namespace App\Http\Controllers\API;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends BaseController 
{
    public function index()
    {
        $movies = Movie::all();
        // Transform the movies to include full S3 URLs
        $movies->transform(function ($movie) {
            if ($movie->file) {
                $movie->file = Storage::disk('s3')->temporaryUrl($movie->file, now()->addMinutes(5));
            }
            return $movie;
        });
        return $this->sendResponse($movies, 'Movies retrieved successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'year' => 'required|integer',
            'genre' => 'required|string',
            'file' => 'required|string',
            'movie_length' => 'required|numeric'
        ]);

        $movie = Movie::create($request->all());
        // Transform the movie to include full S3 URL
        if ($movie->file) {
            $movie->file = Storage::disk('s3')->temporaryUrl($movie->file, now()->addMinutes(5));
        }
        return $this->sendResponse($movie, 'Movie created successfully');
    }

    public function show($id)
    {
        $movie = Movie::find($id);
        if (is_null($movie)) {
            return $this->sendError('Movie not found');
        }
        // Transform the movie to include full S3 URL
        if ($movie->file) {
            $movie->file = Storage::disk('s3')->temporaryUrl($movie->file, now()->addMinutes(5));
        }
        return $this->sendResponse($movie, 'Movie retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'string',
            'year' => 'integer',
            'genre' => 'string',
            'file' => 'string',
            'movie_length' => 'numeric'
        ]);

        $movie = Movie::find($id);
        if (is_null($movie)) {
            return $this->sendError('Movie not found');
        }

        $movie->update($request->all());
        // Transform the movie to include full S3 URL
        if ($movie->file) {
            $movie->file = Storage::disk('s3')->temporaryUrl($movie->file, now()->addMinutes(5));
        }
        return $this->sendResponse($movie, 'Movie updated successfully');
    }

    public function destroy($id)
    {
        $movie = Movie::find($id);
        if (is_null($movie)) {
            return $this->sendError('Movie not found');
        }

        $movie->delete();
        return $this->sendResponse([], 'Movie deleted successfully');
    }
}
