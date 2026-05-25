<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index()
    {
        $user = auth()->user();
        $cart = $user->cart()->with('items.product.productImages')->first();
        
        $items = $cart ? $cart->items : collect();
        $total = 0;
        foreach ($items as $item) {
            $total += $item->quantity * $item->product->price;
        }
        
        $balance = $user->balance;
        
        return view('cart.index', compact('items', 'total', 'balance'));
    }

    /**
     * Add an item to the shopping cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Bu ürün şu anda satışta değil.'
            ], 422);
        }

        if ($product->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Bu ürün tükenmiştir.'
            ], 422);
        }

        $user = auth()->user();
        $cart = $user->cart()->firstOrCreate();

        $cartItem = $cart->items()->where('product_id', $product->id)->first();
        $currentQuantity = $cartItem ? $cartItem->quantity : 0;
        $newQuantity = $currentQuantity + $request->quantity;

        if ($newQuantity > $product->stock) {
            return response()->json([
                'success' => false,
                'message' => "Yetersiz stok. Sepetinizdeki adetle birlikte en fazla {$product->stock} adet ekleyebilirsiniz."
            ], 422);
        }

        if ($newQuantity > 10) {
            return response()->json([
                'success' => false,
                'message' => 'Bir üründen tek seferde en fazla 10 adet satın alabilirsiniz.'
            ], 422);
        }

        if ($cartItem) {
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $newQuantity
            ]);
        }

        $cartCount = $cart->items()->sum('quantity');

        return response()->json([
            'success' => true,
            'cartCount' => $cartCount,
            'message' => 'Ürün sepete başarıyla eklendi!'
        ]);
    }

    /**
     * Update quantity of a cart item.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->cart->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Yetkisiz işlem.'], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $product = $cartItem->product;
        
        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün satışta değil.'
            ], 422);
        }

        if ($request->quantity > $product->stock) {
            return response()->json([
                'success' => false,
                'message' => "Stokta sadece {$product->stock} adet ürün bulunuyor."
            ], 422);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        $cart = $cartItem->cart;
        $total = 0;
        // Refresh the items to calculate total correctly
        $cart->load('items.product');
        foreach ($cart->items as $item) {
            $total += $item->quantity * $item->product->price;
        }

        $subtotal = $cartItem->quantity * $product->price;

        return response()->json([
            'success' => true,
            'subtotal' => (float) $subtotal,
            'total' => (float) $total
        ]);
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(CartItem $cartItem)
    {
        if ($cartItem->cart->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Yetkisiz işlem.'], 403);
        }

        $cart = $cartItem->cart;
        $cartItem->delete();

        // Refresh items after deletion
        $cart->load('items.product');
        $total = 0;
        foreach ($cart->items as $item) {
            $total += $item->quantity * $item->product->price;
        }

        $cartCount = $cart->items()->sum('quantity');

        return response()->json([
            'success' => true,
            'cartCount' => $cartCount,
            'total' => (float) $total
        ]);
    }
}
