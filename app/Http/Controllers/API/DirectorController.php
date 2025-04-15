<?php

namespace App\Http\Controllers\API;

use App\Models\Director;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\Validator;

class DirectorController extends BaseController
{
    public function index()
    {
        $directors = Director::all();
        return $this->sendResponse($directors, 'Directors retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string',
            'age' => 'required|integer',
            'nationality' => 'required|string',
            'history' => 'nullable|string'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $director = Director::create($request->all());
        return $this->sendResponse($director, 'Director created successfully');
    }
} 