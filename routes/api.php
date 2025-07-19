<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiCategoryController;
use App\Http\Controllers\ApiProductController;
use App\Http\Controllers\Api\FormApiController;
// English (default)
Route::get('/categories', [ApiCategoryController::class, 'index']);
Route::get('/home_categories', [ApiCategoryController::class, 'e_category']);

// Arabic
Route::prefix('ar')->group(function () {
    Route::get('/categories', [ApiCategoryController::class, 'index'])->defaults('lang', 'ar');
    Route::get('/home_categories', [ApiCategoryController::class, 'e_category'])->defaults('lang', 'ar');
});

// French
Route::prefix('fr')->group(function () {
    Route::get('/categories', [ApiCategoryController::class, 'index'])->defaults('lang', 'fr');
    Route::get('/home_categories', [ApiCategoryController::class, 'e_category'])->defaults('lang', 'fr');
});

// Russian
Route::prefix('ru')->group(function () {
    Route::get('/categories', [ApiCategoryController::class, 'index'])->defaults('lang', 'ru');
    Route::get('/home_categories', [ApiCategoryController::class, 'e_category'])->defaults('lang', 'ru');
});

// English (default)
Route::get('/products', [ApiProductController::class, 'index']);
Route::get('/products/bestsellers', [ApiProductController::class, 'bestSellers']);
Route::get('/products/{id}', [ApiProductController::class, 'show']);
Route::get('/categories/{categoryId}/products', [ApiProductController::class, 'productsByCategory']);
Route::get('/current-date', [ApiProductController::class, 'getCurrentDate']);

// Arabic
Route::prefix('ar')->group(function () {
    Route::get('/products', [ApiProductController::class, 'index'])->defaults('lang', 'ar');
    Route::get('/products/bestsellers', [ApiProductController::class, 'bestSellers'])->defaults('lang', 'ar');
    Route::get('/products/{id}', [ApiProductController::class, 'show'])->defaults('lang', 'ar');
    Route::get('/categories/{categoryId}/products', [ApiProductController::class, 'productsByCategory'])->defaults('lang', 'ar');
    Route::get('/current-date', [ApiProductController::class, 'getCurrentDate'])->defaults('lang', 'ar');
});

// French
Route::prefix('fr')->group(function () {
    Route::get('/products', [ApiProductController::class, 'index'])->defaults('lang', 'fr');
    Route::get('/products/bestsellers', [ApiProductController::class, 'bestSellers'])->defaults('lang', 'fr');
    Route::get('/products/{id}', [ApiProductController::class, 'show'])->defaults('lang', 'fr');
    Route::get('/categories/{categoryId}/products', [ApiProductController::class, 'productsByCategory'])->defaults('lang', 'fr');
    Route::get('/current-date', [ApiProductController::class, 'getCurrentDate'])->defaults('lang', 'fr');
});

// Russian
Route::prefix('ru')->group(function () {
    Route::get('/products', [ApiProductController::class, 'index'])->defaults('lang', 'ru');
    Route::get('/products/bestsellers', [ApiProductController::class, 'bestSellers'])->defaults('lang', 'ru');
    Route::get('/products/{id}', [ApiProductController::class, 'show'])->defaults('lang', 'ru');
    Route::get('/categories/{categoryId}/products', [ApiProductController::class, 'productsByCategory'])->defaults('lang', 'ru');
    Route::get('/current-date', [ApiProductController::class, 'getCurrentDate'])->defaults('lang', 'ru');
});


Route::post('/contact', [FormApiController::class, 'contact']);
Route::post('/product', [FormApiController::class, 'product']);
Route::post('/company', [FormApiController::class, 'company']);
Route::post('/full-product', [FormApiController::class, 'fullProduct']);
