<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\MovieController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\RatingController;
use App\Http\Controllers\API\DirectorController;

// Public routes
Route::controller(RegisterController::class)->group(function(): void{
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('logout', 'logout');
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::controller(UserController::class)->group(function(){
        Route::post('user/upload_avatar', 'uploadAvatar');
        Route::delete('user/remove_avatar','removeAvatar');
        Route::post('user/send_verification_email','sendVerificationEmail');
        Route::post('user/change_email', 'changeEmail');
    });
    
    // Movie routes
    Route::resource('movies', MovieController::class);
    
    // File routes
    Route::resource('files', FileController::class);

    // Rating routes
    Route::get('ratings', [RatingController::class, 'index']);
    Route::get('directors', [DirectorController::class, 'index']);
    Route::post('directors', [DirectorController::class, 'store']);
});