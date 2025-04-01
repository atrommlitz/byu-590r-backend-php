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
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'movie_length' => 'required|numeric'
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('movies/posters', $fileName, 's3');
            Storage::disk('s3')->setVisibility($path, 'public');

            $movie = Movie::create([
                'title' => $request->title,
                'year' => $request->year,
                'genre' => $request->genre,
                'file' => $path,
                'movie_length' => $request->movie_length
            ]);

            if ($movie->file) {
                $movie->file = Storage::disk('s3')->temporaryUrl($movie->file, now()->addMinutes(5));
            }
            return $this->sendResponse($movie, 'Movie created successfully');
        }
        return $this->sendError('Movie poster is required');
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
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'movie_length' => 'numeric'
        ]);

        $movie = Movie::find($id);
        if (is_null($movie)) {
            return $this->sendError('Movie not found');
        }

        // Handle file upload if new file is provided
        if ($request->hasFile('file')) {
            // Delete old file
            if ($movie->file) {
                Storage::disk('s3')->delete($movie->file);
            }

            // Upload new file
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('movies/posters', $fileName, 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $movie->file = $path;
        }

        $movie->update([
            'title' => $request->title ?? $movie->title,
            'year' => $request->year ?? $movie->year,
            'genre' => $request->genre ?? $movie->genre,
            'movie_length' => $request->movie_length ?? $movie->movie_length
        ]);

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

        // Delete file from S3
        if ($movie->file) {
            Storage::disk('s3')->delete($movie->file);
        }

        $movie->delete();
        return $this->sendResponse([], 'Movie deleted successfully');
    }
}
