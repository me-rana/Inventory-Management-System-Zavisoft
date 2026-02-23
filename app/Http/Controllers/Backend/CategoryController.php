<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    //

    public function index()
    {
        $categories = Category::latest()->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'image' => 'nullable|image|max:2048'
        ]);

        // generate slug
        $data['slug'] = $this->generateUniqueSlug($data['name']);

        // handle upload
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        // remove non-db field
        unset($data['image']);

        Category::create($data);

        return back()->with('success', 'Category created');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'image' => 'nullable|image|max:2048'
        ]);

        // keep old slug or generate if missing
        if (!$category->slug) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $category->id);
        }

        if ($request->hasFile('image')) {

            // delete old image
            if ($category->image_path && Storage::disk('public')->exists($category->image_path)) {
                Storage::disk('public')->delete($category->image_path);
            }

            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        // remove temp upload field
        unset($data['image']);

        $category->update($data);

        return back()->with('success', 'Category updated');
    }

    public function destroy(Category $category)
    {
        // delete stored image if exists
        if ($category->image_path && Storage::disk('public')->exists($category->image_path)) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        return back()->with('success', 'Category deleted');
    }

    private function generateUniqueSlug($name, $ignoreId = null)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Category::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }
}
