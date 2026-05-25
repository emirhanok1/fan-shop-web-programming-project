<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page.
     */
    public function index()
    {
        $user = auth()->user();
        $cart = $user->cart()->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Sepetiniz boş.');
        }

        // Check if any product is inactive or out of stock
        foreach ($cart->items as $item) {
            if (!$item->product->is_active || $item->product->stock < $item->quantity) {
                return redirect()->route('cart.index')->with('error', "{$item->product->name} ürününde yetersiz stok veya aktif olmayan durum var. Lütfen sepetinizi kontrol edin.");
            }
        }

        $addresses = $user->addresses()->get();
        $balance = $user->balance;

        $total = 0;
        foreach ($cart->items as $item) {
            $total += $item->quantity * $item->product->price;
        }

        $balance_used = min($balance, $total);
        $card_amount = $total - $balance_used;

        return view('checkout.index', compact('addresses', 'balance', 'total', 'balance_used', 'card_amount', 'cart'));
    }

    /**
     * Process checkout/payment.
     */
    public function process(Request $request)
    {
        $user = auth()->user();
        $cart = $user->cart()->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Sepetiniz boş.');
        }

        // ADIM 1: Sepeti kontrol et (aktiflik ve stok)
        foreach ($cart->items as $item) {
            if (!$item->product->is_active) {
                return redirect()->route('cart.index')->with('error', "{$item->product->name} ürünü şu anda satışta değil.");
            }
            if ($item->product->stock < $item->quantity) {
                return redirect()->route('cart.index')->with('error', "{$item->product->name} ürünü için yetersiz stok (Stok: {$item->product->stock}).");
            }
        }

        // ADIM 2: Toplam hesapla
        $total = 0;
        foreach ($cart->items as $item) {
            $total += $item->quantity * $item->product->price;
        }

        // ADIM 3: Bakiye hesapla
        $balance_used = min($user->balance, $total);
        $card_amount = $total - $balance_used;

        // ADIM 4: Kredi kartı kontrolü ve Validasyon
        $rules = [
            'shipping_address' => 'required|string|min:10',
        ];

        if ($card_amount > 0) {
            $rules['card_number'] = 'required|string|size:16';
            $rules['card_expiry'] = ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/?([0-9]{2})$/'];
            $rules['card_cvv'] = 'required|string|size:3';
        }

        $request->validate($rules, [
            'shipping_address.required' => 'Teslimat adresi zorunludur.',
            'shipping_address.min' => 'Teslimat adresi en az 10 karakter olmalıdır.',
            'card_number.required' => 'Kart numarası zorunludur.',
            'card_number.size' => 'Kart numarası 16 haneli olmalıdır.',
            'card_expiry.required' => 'Son kullanma tarihi zorunludur.',
            'card_expiry.regex' => 'Son kullanma tarihi MM/YY formatında olmalıdır.',
            'card_cvv.required' => 'CVV zorunludur.',
            'card_cvv.size' => 'CVV 3 haneli olmalıdır.',
        ]);

        // DB Transaction ile atomik sipariş kaydı
        DB::beginTransaction();

        try {
            // ADIM 5: Order oluştur
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $total,
                'balance_used' => $balance_used,
                'card_amount' => $card_amount,
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
            ]);

            // ADIM 6: invoice_no oluştur
            $order->invoice_no = 'FS-' . date('Ymd') . '-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
            $order->save();

            // ADIM 7: OrderItem'ları oluştur & ADIM 8: Stokları düş
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->product->price,
                ]);

                // Stok düşme
                $item->product->decrement('stock', $item->quantity);
            }

            // ADIM 9: Bakiyeyi düş
            if ($balance_used > 0) {
                $user->decrement('balance', $balance_used);

                // ADIM 10: Transaction kaydı oluştur
                Transaction::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'amount' => -$balance_used,
                    'type' => 'payment',
                    'description' => 'Sipariş ödemesi: ' . $order->invoice_no,
                ]);
            }

            // ADIM 11: Sepeti temizle
            $cart->items()->delete();

            DB::commit();

            // ADIM 12: Yönlendir
            return redirect()->route('orders.show', $order)
                ->with('success', 'Siparişiniz başarıyla oluşturuldu!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Sipariş kaydedilirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}
