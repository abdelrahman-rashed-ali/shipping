<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ApiCategoryController;
use \App\Http\Controllers\ApiProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/categories', [ApiCategoryController::class, 'index']);
Route::get('/products', [ApiProductController::class, 'index']);
Route::get('/categories/{categoryId}/products', [ApiProductController::class, 'productsByCategory']);
