<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'productImages'])
            ->where('is_active', true)
            ->where('stock', '>', 0);

        // Category filter (by slug)
        $activeCategory = null;
        if ($request->filled('category')) {
            $activeCategory = Category::where('slug', $request->category)->first();
            if ($activeCategory) {
                $query->where('category_id', $activeCategory->id);
            }
        }

        // Franchise filter
        if ($request->filled('franchise')) {
            $query->where('franchise', $request->franchise);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('franchise', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        switch ($request->input('sort', 'newest')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->withCount('orderItems')->orderBy('order_items_count', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::all();
        $franchises = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->select('franchise')
            ->distinct()
            ->pluck('franchise');

        return view('products.index', compact('products', 'categories', 'franchises', 'activeCategory'));
    }

    /**
     * Display the specified product.
     */
    public function show(string $slug)
    {
        $product = Product::with(['category', 'productImages'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $tmdbData = null;
        if ($product->tmdb_id && $product->tmdb_type) {
            $tmdbData = app(\App\Services\TMDBService::class)
                ->getById($product->tmdb_id, $product->tmdb_type);
        }

        $relatedProducts = Product::with(['productImages'])
            ->where('franchise', $product->franchise)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->take(4)
            ->get();

        try {
            $html = view('products.show', compact('product', 'relatedProducts', 'tmdbData'))->render();
            return response($html);
        } catch (\Throwable $e) {
            return response($e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n" . $e->getTraceAsString(), 500);
        }
    }
}
