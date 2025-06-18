<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ApiProductController extends Controller
{
    /**
     * Display a listing of all products.
     */
    public function index()
    {
        $products = Product::with(['hasOneCategory', 'hasManyImages', 'hasManyData', 'hasManyTags'])->get();
        return response()->json($products);
    }

    /**
     * Display products for a specific category.
     */
    public function productsByCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $products = Product::with(['hasOneCategory', 'hasManyImages', 'hasManyData', 'hasManyTags'])
            ->where('category_id', $categoryId)
            ->get();
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
