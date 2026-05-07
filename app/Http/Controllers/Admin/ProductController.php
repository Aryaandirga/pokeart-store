<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images'])
            ->where('is_active', true);

        if ($request->search) {
            $query->where('name', 'ilike', '%' . $request->search . '%');
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->status === 'published') {
            $query->where('is_published', true);
        } elseif ($request->status === 'unpublished') {
            $query->where('is_published', false);
        }

        $products   = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::all();

        return Inertia::render('admin/Products', [
            'products'   => $products,
            'categories' => $categories,
            'filters'    => $request->only(['search', 'category', 'status']),
        ]);
    }

    public function toggle(Product $product)
    {
        $product->update(['is_published' => !$product->is_published]);

        return back()->with('success',
            $product->is_published
                ? "{$product->name} dipublish ke website."
                : "{$product->name} disembunyikan dari website."
        );
    }

    public function uploadImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $path = $request->file('image')->store('products', 'public');

        // Set as main image jika belum ada
        if (!$product->image) {
            $product->update(['image' => $path]);
        }

        $image = ProductImage::create([
            'product_id' => $product->id,
            'image_path' => $path,
            'sort_order' => $product->images()->count(),
        ]);

        return back()->with('success', 'Gambar berhasil diupload.');
    }

    public function deleteImage(Product $product, ProductImage $image)
    {
        \Storage::disk('public')->delete($image->image_path);

        if ($product->image === $image->image_path) {
            $next = $product->images()->where('id', '!=', $image->id)->first();
            $product->update(['image' => $next?->image_path]);
        }

        $image->delete();

        return back()->with('success', 'Gambar dihapus.');
    }
}