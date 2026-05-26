<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['items.product'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display details of a specific order.
     */
    public function show(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403, 'Yetkisiz erişim.');

        $order->load(['items.product.productImages', 'tracking']);

        // Teslimat adresinden şehir parse et
        $addressParts = explode(',', $order->shipping_address);
        $city = trim(end($addressParts));
        if (empty($city)) {
            $city = null;
        }

        $weatherService = app(\App\Services\WeatherService::class);
        $weather = $city ? $weatherService->getByCity($city) : null;
        
        $isDelayWarning = $weatherService->isDelayWarning($weather);
        $weatherDescription = $weatherService->getDescription($weather);
        $temperature = $weatherService->getTemperature($weather);
        $weatherIcon = $weatherService->getIconUrl($weather);

        return view('orders.show', compact(
            'order', 
            'weather', 
            'isDelayWarning', 
            'weatherDescription', 
            'temperature', 
            'weatherIcon'
        ));
    }

    /**
     * Cancel a pending order (refunds to balance).
     */
    public function cancel(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403, 'Yetkisiz erişim.');

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Sadece onay bekleyen siparişler iptal edilebilir.');
        }

        DB::beginTransaction();

        try {
            // Update order status
            $order->update(['status' => 'cancelled']);

            // Restore product stocks
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            // Refund total amount to balance
            $user = auth()->user();
            $user->increment('balance', $order->total_amount);

            // Log Transaction
            Transaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'type' => 'refund',
                'description' => 'Sipariş iadesi: ' . $order->invoice_no,
            ]);

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Siparişiniz iptal edildi, ' . number_format($order->total_amount, 2) . ' ₺ bakiyenize eklendi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Sipariş iptal edilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Confirm delivery of an order.
     */
    public function confirm(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403, 'Yetkisiz erişim.');

        if ($order->status !== 'approved' || !$order->tracking || $order->tracking->step !== 'delivered') {
            return redirect()->back()->with('error', 'Bu sipariş henüz onaylanamaz veya teslim edilmemiş.');
        }

        DB::beginTransaction();

        try {
            // Update status to confirmed
            $order->update(['status' => 'confirmed']);

            // We can also advance tracking to completed if we want, or just leave it
            // The admin order controller has 'completed' step but let's see.
            // Our DB tracking enum is: ['sourcing', 'packaging', 'shipped', 'on_the_way', 'delivered']
            // So 'delivered' is the last step in the enum. We don't need to advance tracking step anymore.
            // Just marking the order status as confirmed is perfect!

            DB::commit();

            return redirect()->back()->with('success', 'Siparişi başarıyla teslim aldığınızı onayladınız. Teşekkür ederiz!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'İşlem sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }
}
