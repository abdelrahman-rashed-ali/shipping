<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    // Show all categories + form
    public function index()
    {
        $categories = Category::all();
        return view('categories', compact('categories'));
    }

    // Store a new category
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'cropped_image' => 'required|string',
        ]);

        $imagePath = $this->saveBase64Image($request->cropped_image);

        Category::create([
            'name'        => $request->name,
            'description' => $request->description,
            'image'       => $imagePath,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category added successfully!');
    }

    // Update existing category
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'cropped_image' => 'nullable|string',
        ]);

        $category = Category::findOrFail($id);

        $category->name = $request->name;
        $category->description = $request->description;

        if ($request->filled('cropped_image')) {
            // Delete old image file if exists
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }

            $imagePath = $this->saveBase64Image($request->cropped_image);
            $category->image = $imagePath;
        }

        $category->save();

        return redirect()->route('categories.index')->with('success', 'Category updated successfully!');
    }

    // Delete a category
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
    }

    // Helper: Save base64 image string to public storage and return path
    protected function saveBase64Image($base64Image)
    {
        // Separate the data from the mime type
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $data = substr($base64Image, strpos($base64Image, ',') + 1);
            $extension = strtolower($type[1]); // jpg, png, gif

            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new \Exception('Invalid image type');
            }

            $data = base64_decode($data);

            if ($data === false) {
                throw new \Exception('base64 decode failed');
            }
        } else {
            throw new \Exception('Invalid base64 image format');
        }

        $filename = 'images/categories/' . Str::random(10) . '.' . $extension;

        // Ensure directory exists
        if (!File::exists(public_path('images/categories'))) {
            File::makeDirectory(public_path('images/categories'), 0755, true);
        }

        file_put_contents(public_path($filename), $data);

        return '/' . $filename; // Store relative path for web access
    }
}
