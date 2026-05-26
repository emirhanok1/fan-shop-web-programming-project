<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Http\Requests\Admin\StoreProductRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Product::with(['category', 'productImages']);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('franchise')) {
            $query->where('franchise', 'like', '%' . $request->franchise . '%');
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'category_id' => $request->category_id,
            'franchise' => $request->franchise,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'tmdb_id' => $request->tmdb_id,
            'tmdb_type' => $request->tmdb_type,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('images')) {
            $images = $request->file('images');
            Storage::disk('public')->makeDirectory('products');

            foreach ($images as $index => $imageFile) {
                // Resize and encode
                $img = Image::read($imageFile);
                $img->scaleDown(width: 1024);
                $encoded = $img->toWebp(80);

                // Save file
                $fileName = Str::uuid() . '.webp';
                $filePath = 'products/' . $fileName;
                Storage::disk('public')->put($filePath, $encoded->toString());

                // Create record
                $product->productImages()->create([
                    'image_path' => 'storage/' . $filePath,
                    'is_primary' => ($index === 0),
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Ürün başarıyla oluşturuldu.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(StoreProductRequest $request, Product $product)
    {
        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'category_id' => $request->category_id,
            'franchise' => $request->franchise,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'tmdb_id' => $request->tmdb_id,
            'tmdb_type' => $request->tmdb_type,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('images')) {
            $images = $request->file('images');
            Storage::disk('public')->makeDirectory('products');

            $hasPrimary = $product->productImages()->where('is_primary', true)->exists();

            foreach ($images as $index => $imageFile) {
                // Resize and encode
                $img = Image::read($imageFile);
                $img->scaleDown(width: 1024);
                $encoded = $img->toWebp(80);

                // Save file
                $fileName = Str::uuid() . '.webp';
                $filePath = 'products/' . $fileName;
                Storage::disk('public')->put($filePath, $encoded->toString());

                // Create record
                $product->productImages()->create([
                    'image_path' => 'storage/' . $filePath,
                    'is_primary' => (!$hasPrimary && $index === 0),
                ]);
                $hasPrimary = true;
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Ürün başarıyla güncellendi.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        foreach ($product->productImages as $image) {
            $relativeStoragePath = str_replace('storage/', '', $image->image_path);
            Storage::disk('public')->delete($relativeStoragePath);
            $image->delete();
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Ürün başarıyla silindi.');
    }

    /**
     * Toggle the active status of a product.
     */
    public function toggle(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();

        return response()->json([
            'success' => true,
            'is_active' => $product->is_active
        ]);
    }

    /**
     * Search products using the TMDB API.
     */
    public function tmdbSearch(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return response()->json([]);
        }

        $tmdbService = app(\App\Services\TMDBService::class);
        $results = $tmdbService->search($query);

        $formatted = array_map(function($item) use ($tmdbService) {
            return [
                'id' => $item['id'],
                'title' => $tmdbService->getTitle($item, $item['media_type']),
                'media_type' => $item['media_type'],
                'poster' => $tmdbService->getPosterUrl($item['poster_path'] ?? null, 'w92'),
                'year' => substr($item['first_air_date'] ?? $item['release_date'] ?? '', 0, 4),
                'overview' => \Illuminate\Support\Str::limit($item['overview'] ?? '', 100)
            ];
        }, $results);

        return response()->json($formatted);
    }

    /**
     * Delete a specific product image.
     */
    public function deleteImage(ProductImage $productImage)
    {
        $product = $productImage->product;
        $isPrimary = $productImage->is_primary;

        // Delete from storage
        $relativeStoragePath = str_replace('storage/', '', $productImage->image_path);
        Storage::disk('public')->delete($relativeStoragePath);
        
        // Delete from database
        $productImage->delete();

        // Reassign primary image if the deleted one was primary
        if ($isPrimary) {
            $nextImage = $product->productImages()->first();
            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        return response()->json(['success' => true]);
    }
}
