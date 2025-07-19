<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\UserController;
use App\Models\ContactMessage;
use App\Models\ProductRequest;
use App\Models\CompanyRequest;

use App\Models\FullProductRequest;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    try {
        $contacts = \DB::table('contact_messages')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return (object) $item; // Convert to object for view compatibility
            });
    } catch (\Exception $e) {
        \Log::error('Error fetching contact_messages: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
        $contacts = collect();
    }

    try {
        $products = \DB::table('product_requests')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return (object) $item;
            });
    } catch (\Exception $e) {
        \Log::error('Error fetching product_requests: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
        $products = collect();
    }

    try {
        $companies = \DB::table('company_requests')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return (object) $item;
            });
    } catch (\Exception $e) {
        \Log::error('Error fetching company_requests: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
        $companies = collect();
    }

    try {
        $fullRequests = \DB::table('full_product_requests')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return (object) $item;
            });
    } catch (\Exception $e) {
        \Log::error('Error fetching full_product_requests: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
        $fullRequests = collect();
    }

    return view('dashboard', [
        'contacts' => $contacts,
        'products' => $products,
        'companies' => $companies,
        'fullRequests' => $fullRequests,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/users', [UserController::class , 'get'])->middleware(['auth', 'verified'])->name('users');

Route::middleware(['auth'])->group(function () {
    Route::resource('products', ProductController::class);
});

Route::middleware('auth')->group(function () {
    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
