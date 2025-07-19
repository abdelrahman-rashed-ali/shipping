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
        $products = Product::with(['hasOneCategory', 'hasManyImages', 'hasManyData', 'hasManyTags'])->get();
        $categories = Category::all();
        return view('products', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subdescription' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'best_seller' => 'nullable|boolean',
            'price' => 'required_if:best_seller,1|nullable|numeric|min:0',
            'cropped_images.*' => 'nullable|string',
            'cropped_images' => 'array|max:5',
            'main_image' => 'required|integer|min:0|max:4',
            'data.*.name' => 'nullable|string|max:255',
            'data.*.description' => 'nullable|string|max:255',
            'data' => 'array|max:8',
            'tags.*.name' => 'nullable|string|max:255',
            'tags.*.description' => 'nullable|string',
            'tags' => 'array|max:6',
            'months' => 'nullable|array',
            'months.*' => 'integer|min:1|max:12',
            'deleted_images' => 'nullable|array',
            'deleted_images.*' => 'integer|exists:albums,id',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'subdescription' => $validated['subdescription'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'best_seller' => $request->boolean('best_seller'),
            'price' => $request->boolean('best_seller') ? $validated['price'] : null,
            'months' => $request->has('months') ? json_encode($validated['months']) : null,
        ]);

        // Handle images
        if ($request->has('cropped_images')) {
            $currentImageCount = 0;
            foreach ($request->cropped_images as $index => $croppedImage) {
                if ($croppedImage && $currentImageCount < 5) {
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImage));
                    $imageName = 'products/' . Str::random(10) . '.jpg';
                    Storage::disk('public')->put($imageName, $imageData);
                    Album::create([
                        'product_id' => $product->id,
                        'image' => $imageName,
                        'is_main' => $index == $request->main_image,
                    ]);
                    $currentImageCount++;
                }
            }
        }

        // Handle data
        if ($request->has('data')) {
            $dataCount = 0;
            foreach ($request->data as $dataItem) {
                if (!empty($dataItem['name']) && !empty($dataItem['description']) && $dataCount < 8) {
                    Data::create([
                        'product_id' => $product->id,
                        'name' => $dataItem['name'],
                        'description' => $dataItem['description'],
                    ]);
                    $dataCount++;
                }
            }
        }

        // Handle tags
        if ($request->has('tags')) {
            $tagCount = 0;
            foreach ($request->tags as $tagItem) {
                if (!empty($tagItem['name']) && !empty($tagItem['description']) && $tagCount < 6) {
                    Tag::create([
                        'product_id' => $product->id,
                        'name' => $tagItem['name'],
                        'description' => $tagItem['description'],
                    ]);
                    $tagCount++;
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
            'subdescription' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'best_seller' => 'nullable|boolean',
            'price' => 'required_if:best_seller,1|nullable|numeric|min:0',
            'cropped_images.*' => 'nullable|string',
            'cropped_images' => 'array',
            'main_image' => 'required|integer|min:0',
            'data.*.name' => 'nullable|string|max:255',
            'data.*.description' => 'nullable|string|max:255',
            'data' => 'array|max:8',
            'tags.*.name' => 'nullable|string|max:255',
            'tags.*.description' => 'nullable|string',
            'tags' => 'array|max:6',
            'months' => 'nullable|array',
            'months.*' => 'integer|min:1|max:12',
            'deleted_images' => 'nullable|array',
            'deleted_images.*' => 'integer|exists:albums,id',
        ]);

        $product->update([
            'name' => $validated['name'],
            'subdescription' => $validated['subdescription'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'best_seller' => $request->boolean('best_seller'),
            'price' => $request->boolean('best_seller') ? $validated['price'] : null,
            'months' => $request->has('months') ? json_encode($validated['months']) : null,
        ]);

        // Handle image deletion
        if ($request->has('deleted_images')) {
            foreach ($request->deleted_images as $imageId) {
                $image = Album::find($imageId);
                if ($image && $image->product_id == $product->id) {
                    Storage::disk('public')->delete($image->image);
                    $image->delete();
                }
            }
        }

        // Handle new images
        if ($request->has('cropped_images')) {
            $currentImageCount = $product->hasManyImages()->count();
            foreach ($request->cropped_images as $index => $croppedImage) {
                if ($croppedImage && $currentImageCount < 5) {
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $croppedImage));
                    $imageName = 'products/' . Str::random(10) . '.jpg';
                    Storage::disk('public')->put($imageName, $imageData);
                    Album::create([
                        'product_id' => $product->id,
                        'image' => $imageName,
                        'is_main' => false,
                    ]);
                    $currentImageCount++;
                }
            }
        }

        // Update main image
        $product->hasManyImages()->update(['is_main' => false]);
        $mainImage = $product->hasManyImages()->skip($request->main_image)->first();
        if ($mainImage) {
            $mainImage->update(['is_main' => true]);
        }

        // Handle data
        if ($request->has('data')) {
            $product->hasManyData()->delete();
            $dataCount = 0;
            foreach ($request->data as $dataItem) {
                if (!empty($dataItem['name']) && !empty($dataItem['description']) && $dataCount < 8) {
                    Data::create([
                        'product_id' => $product->id,
                        'name' => $dataItem['name'],
                        'description' => $dataItem['description'],
                    ]);
                    $dataCount++;
                }
            }
        }

        // Handle tags
        if ($request->has('tags')) {
            $product->hasManyTags()->delete();
            $tagCount = 0;
            foreach ($request->tags as $tagItem) {
                if (!empty($tagItem['name']) && !empty($tagItem['description']) && $tagCount < 6) {
                    Tag::create([
                        'product_id' => $product->id,
                        'name' => $tagItem['name'],
                        'description' => $tagItem['description'],
                    ]);
                    $tagCount++;
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        foreach ($product->hasManyImages as $image) {
            Storage::disk('public')->delete($image->image);
        }
        $product->hasManyImages()->delete();
        $product->hasManyData()->delete();
        $product->hasManyTags()->delete();
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
