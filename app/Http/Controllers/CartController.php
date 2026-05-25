<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index()
    {
        // To be implemented
    }

    /**
     * Add an item to the shopping cart.
     */
    public function add(Request $request)
    {
        // To be implemented
    }

    /**
     * Update quantity of a cart item.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        // To be implemented
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(CartItem $cartItem)
    {
        // To be implemented
    }
}
