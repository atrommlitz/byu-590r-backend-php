<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends BaseController
{
    /**
     * Display a listing of the files.
     */
    public function index()
    {
        $files = Storage::disk('s3')->files('uploads');
        $fileList = collect($files)->map(function($file) {
            return [
                'name' => basename($file),
                'path' => $file,
                'url' => $this->getS3Url($file),
                'size' => Storage::disk('s3')->size($file),
                'last_modified' => Storage::disk('s3')->lastModified($file),
            ];
        });
        
        return $this->sendResponse($fileList, 'Files retrieved successfully');
    }

    /**
     * Store a newly uploaded file.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            
            $path = $file->storeAs(
                'uploads',
                $fileName,
                's3'
            );

            if (!$path) {
                return $this->sendError('File upload failed');
            }

            Storage::disk('s3')->setVisibility($path, 'public');
            
            $fileData = [
                'name' => $fileName,
                'path' => $path,
                'url' => $this->getS3Url($path),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ];

            return $this->sendResponse($fileData, 'File uploaded successfully');
        }

        return $this->sendError('No file provided');
    }

    /**
     * Display the specified file.
     */
    public function show($id)
    {
        $path = 'uploads/' . $id;
        
        if (!Storage::disk('s3')->exists($path)) {
            return $this->sendError('File not found');
        }

        $url = $this->getS3Url($path);
        $fileData = [
            'name' => basename($path),
            'path' => $path,
            'url' => $url,
            'size' => Storage::disk('s3')->size($path),
            'last_modified' => Storage::disk('s3')->lastModified($path),
        ];

        return $this->sendResponse($fileData, 'File retrieved successfully');
    }

    /**
     * Update the specified file metadata.
     */
    public function update(Request $request, $id)
    {
        $path = 'uploads/' . $id;
        
        if (!Storage::disk('s3')->exists($path)) {
            return $this->sendError('File not found');
        }

        // Here you could implement metadata updates if needed
        return $this->sendResponse(['path' => $path], 'File metadata updated successfully');
    }

    /**
     * Remove the specified file.
     */
    public function destroy($id)
    {
        $path = 'uploads/' . $id;
        
        if (!Storage::disk('s3')->exists($path)) {
            return $this->sendError('File not found');
        }

        if (Storage::disk('s3')->delete($path)) {
            return $this->sendResponse([], 'File deleted successfully');
        }

        return $this->sendError('File deletion failed');
    }
} 