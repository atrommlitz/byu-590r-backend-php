<?php

namespace App\Http\Controllers\API;

use App\Models\Rating;
use App\Http\Controllers\API\BaseController;

class RatingController extends BaseController
{
    public function index()
    {
        $ratings = Rating::all();
        return $this->sendResponse($ratings, 'Ratings retrieved successfully');
    }
} 