<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    //
    private function generateSlug($name, $ignoreId = null)
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            Product::where('slug',$slug)
                ->when($ignoreId, fn($q)=>$q->where('id','!=',$ignoreId))
                ->exists()
        ){
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function index()
    {
        $products = Product::with('category')->latest()->get();
        $categories = Category::pluck('name','id');
        return view('products.index', compact('products','categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'quantity' => 'nullable|integer',
            'sell_price' => 'nullable|numeric',
            'purchase_price' => 'nullable|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048'
        ]);

        $data['slug'] = $this->generateSlug($data['name']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products','public');
        }

        unset($data['image']);

        Product::create($data);

        return back()->with('success','Product created');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'quantity' => 'nullable|integer',
            'sell_price' => 'nullable|numeric',
            'purchase_price' => 'nullable|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048'
        ]);

        if (!$product->slug) {
            $data['slug'] = $this->generateSlug($data['name'],$product->id);
        }

        if ($request->hasFile('image')) {

            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }

            $data['image_path'] = $request->file('image')->store('products','public');
        }

        unset($data['image']);

        $product->update($data);

        return back()->with('success','Product updated');
    }

    public function destroy(Product $product)
    {
        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return back()->with('success','Product deleted');
    }

}
