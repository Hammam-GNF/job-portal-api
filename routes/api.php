<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobListingController;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/register/company', [AuthController::class, 'registerCompany']);

// Route::middleware('auth::sanctum')->group(function(){
//     Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
//     Route::get('/me', fn (Request $request) => new UserResource($request->user()));
// });

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register/employer', [AuthController::class, 'registerEmployer']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', fn (Request $request) => new UserResource($request->user()));
    });
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::apiResource('users', UserController::class);

    Route::apiResource('categories', CategoryController::class);

    Route::get('companies', [CompanyController::class, 'index']);
    Route::get('companies/{company}', [CompanyController::class, 'show']);

    Route::patch('/users/{user}/suspend', [UserController::class, 'suspend']);
    Route::patch('/users/{user}/restore', [UserController::class, 'restore']);
});

Route::middleware(['auth:sanctum', 'role:employer'])->prefix('employer')->group(function () {
    Route::post('company', [CompanyController::class, 'store']);
    Route::get('company', [CompanyController::class, 'myCompany']);
    Route::put('company/{company}', [CompanyController::class, 'update']);
    Route::delete('company/{company}', [CompanyController::class, 'destroy']);

    Route::apiResource('job-listings', JobListingController::class)->parameters(['job-listings' => 'jobListing',]);

    Route::get('/job-listings/{jobListing}/applications', [ApplicationController::class, 'indexByJob']);

    Route::patch('/applications/{application}/review', [ApplicationController::class, 'review']);
    Route::patch('/applications/{application}/accept', [ApplicationController::class, 'accept']);
    Route::patch('/applications/{application}/reject', [ApplicationController::class, 'reject']);
});

Route::middleware(['auth:sanctum', 'role:applicant'])->prefix('applicant')->group(function () {
    Route::get('job-listings', [JobListingController::class, 'index']);
    Route::get('job-listings/{jobListing}', [JobListingController::class, 'show']);
    
    Route::apiResource('applications', ApplicationController::class)->only(['index', 'show', 'store', 'destroy']);
});
