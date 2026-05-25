<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index()
    {
        // To be implemented
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        // To be implemented
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        // To be implemented
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        // To be implemented
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        // To be implemented
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        // To be implemented
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // To be implemented
    }

    /**
     * Toggle the active status of a product.
     */
    public function toggle(Product $product)
    {
        // To be implemented
    }

    /**
     * Search products using the TMDB API.
     */
    public function tmdbSearch(Request $request)
    {
        // To be implemented
    }
}
