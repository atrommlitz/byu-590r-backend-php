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
        try {
            $request->validate([
                'title' => 'required|string',
                'year' => 'required|integer',
                'genre' => 'required|string',
                'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg',
                'movie_length' => 'required|numeric'
            ]);

            // Handle file upload
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('movies/posters', $fileName, 's3');
            Storage::disk('s3')->setVisibility($path, 'public');

            $movie = Movie::create([
                'title' => $request->title,
                'year' => (int)$request->year,
                'genre' => $request->genre,
                'movie_length' => (float)$request->movie_length,
                'file' => $path
            ]);

            if ($movie->file) {
                $movie->file = Storage::disk('s3')->temporaryUrl($movie->file, now()->addMinutes(5));
            }

            return $this->sendResponse($movie, 'Movie created successfully');
        } catch (\Exception $e) {
            \Log::error('Movie creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating movie',
                'errors' => $e instanceof \Illuminate\Validation\ValidationException ? $e->errors() : [$e->getMessage()]
            ], 422);
        }
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
        try {
            \Log::info('Update request received:', [
                'id' => $id,
                'request' => $request->all(),
                'files' => $request->hasFile('file') ? 'yes' : 'no'
            ]);

            $request->validate([
                'title' => 'required|string',
                'year' => 'required|integer',
                'genre' => 'required|string',
                'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
                'movie_length' => 'required|numeric'
            ]);

            $movie = Movie::find($id);
            if (is_null($movie)) {
                return $this->sendError('Movie not found');
            }

            try {
                // Handle file upload if new file is provided
                if ($request->hasFile('file')) {
                    \Log::info('Processing file upload for movie update');
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

                // Update the movie with new data
                $movie->update([
                    'title' => $request->title,
                    'year' => (int)$request->year,
                    'genre' => $request->genre,
                    'movie_length' => (float)$request->movie_length
                ]);

                // Refresh the model to get the updated data
                $movie->refresh();

                if ($movie->file) {
                    $movie->file = Storage::disk('s3')->temporaryUrl($movie->file, now()->addMinutes(5));
                }

                return $this->sendResponse($movie, 'Movie updated successfully');

            } catch (\Exception $e) {
                \Log::error('Error updating movie:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating movie: ' . $e->getMessage()
                ], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Unexpected error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
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
