<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Album;
use App\Models\Data;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['hasOneCategory', 'hasManyImages'])->get();
        $categories = Category::all();
        return view('products', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'best_seller' => 'nullable|boolean',
            'cropped_images.*' => 'nullable|string',
            'cropped_images' => 'array|max:5', // Limit to 5 images
            'main_image' => 'required|integer|min:0|max:4', // Ensure main_image is valid
            'data.*.name' => 'nullable|string|max:255',
            'data.*.description' => 'nullable|string|max:255',
            'tags.*.name' => 'nullable|string|max:255',
            'tags.*.description' => 'nullable|string|max:255',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'best_seller' => $request->boolean('best_seller'),
        ]);

        // Handle images
        if ($request->has('cropped_images')) {
            foreach ($request->cropped_images as $index => $croppedImage) {
                if ($croppedImage) {
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImage));
                    $imageName = 'products/' . Str::random(10) . '.jpg';
                    Storage::disk('public')->put($imageName, $imageData);
                    Album::create([
                        'product_id' => $product->id,
                        'image' => $imageName,
                        'is_main' => $index == $request->main_image,
                    ]);
                }
            }
        }

        // Handle data
        if ($request->has('data')) {
            foreach ($request->data as $dataItem) {
                if (!empty($dataItem['name']) && !empty($dataItem['description'])) {
                    Data::create([
                        'product_id' => $product->id,
                        'name' => $dataItem['name'],
                        'description' => $dataItem['description'],
                    ]);
                }
            }
        }

        // Handle tags
        if ($request->has('tags')) {
            foreach ($request->tags as $tagItem) {
                if (!empty($tagItem['name']) && !empty($tagItem['description'])) {
                    Tag::create([
                        'product_id' => $product->id,
                        'name' => $tagItem['name'],
                        'description' => $tagItem['description'],
                    ]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return response()->json($product->load(['hasManyImages', 'hasManyData', 'hasManyTags']));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'best_seller' => 'nullable|boolean',
            'cropped_images.*' => 'nullable|string',
            'cropped_images' => 'array|max:5', // Limit to 5 images
            'main_image' => 'required|integer|min:0|max:4', // Ensure main_image is valid
            'data.*.name' => 'nullable|string|max:255',
            'data.*.description' => 'nullable|string|max:255',
            'tags.*.name' => 'nullable|string|max:255',
            'tags.*.description' => 'nullable|string|max:255',
        ]);

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'best_seller' => $request->boolean('best_seller'),
        ]);

        // Handle images
        if ($request->has('cropped_images')) {
            // Delete old images
            $product->hasManyImages()->delete();
            foreach ($request->cropped_images as $index => $croppedImage) {
                if ($croppedImage) {
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImage));
                    $imageName = 'products/' . Str::random(10) . '.jpg';
                    Storage::disk('public')->put($imageName, $imageData);
                    Album::create([
                        'product_id' => $product->id,
                        'image' => $imageName,
                        'is_main' => $index == $request->main_image,
                    ]);
                }
            }
        }

        // Handle data
        if ($request->has('data')) {
            $product->hasManyData()->delete();
            foreach ($request->data as $dataItem) {
                if (!empty($dataItem['name']) && !empty($dataItem['description'])) {
                    Data::create([
                        'product_id' => $product->id,
                        'name' => $dataItem['name'],
                        'description' => $dataItem['description'],
                    ]);
                }
            }
        }

        // Handle tags
        if ($request->has('tags')) {
            $product->hasManyTags()->delete();
            foreach ($request->tags as $tagItem) {
                if (!empty($tagItem['name']) && !empty($tagItem['description'])) {
                    Tag::create([
                        'product_id' => $product->id,
                        'name' => $tagItem['name'],
                        'description' => $tagItem['description'],
                    ]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Delete associated images, data, and tags
        $product->hasManyImages()->delete();
        $product->hasManyData()->delete();
        $product->hasManyTags()->delete();
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
