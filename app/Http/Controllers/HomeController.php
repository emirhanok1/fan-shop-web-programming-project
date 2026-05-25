<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        $featuredProducts = Product::with(['category', 'productImages'])
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::withCount(['products' => function ($q) {
            $q->where('is_active', true)->where('stock', '>', 0);
        }])->get();

        $franchises = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->select('franchise')
            ->distinct()
            ->pluck('franchise');

        return view('home', compact('featuredProducts', 'categories', 'franchises'));
    }
}
