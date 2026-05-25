<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display a listing of all orders.
     */
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'tracking']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Approve a pending order.
     */
    public function approve(Order $order)
    {
        if ($order->status === 'pending') {
            $order->update(['status' => 'approved']);
            
            // Initialize tracking at step 1 if not exists
            if (!$order->tracking) {
                $order->tracking()->create([
                    'step' => 'preparing',
                    'description' => 'Siparişiniz hazırlanıyor.',
                ]);
            }

            return redirect()->back()->with('success', 'Sipariş onaylandı.');
        }

        return redirect()->back()->with('error', 'Sipariş onaylanamaz.');
    }

    /**
     * Advance the tracking step of an approved order.
     */
    public function advance(Order $order)
    {
        if ($order->tracking) {
            $steps = ['preparing', 'shipped', 'delivered', 'completed'];
            $currentStep = $order->tracking->step;
            $currentIndex = array_search($currentStep, $steps);

            if ($currentIndex !== false && $currentIndex < count($steps) - 1) {
                $nextStep = $steps[$currentIndex + 1];
                
                $order->tracking->update([
                    'step' => $nextStep,
                    'description' => $this->getStepDescription($nextStep),
                ]);

                // If completed, update order status to confirmed
                if ($nextStep === 'completed') {
                    $order->update(['status' => 'confirmed']);
                }

                return redirect()->back()->with('success', 'Takip aşaması güncellendi.');
            }
        }

        return redirect()->back()->with('error', 'Takip aşaması güncellenemez.');
    }

    /**
     * Reject/Cancel an order from the admin side.
     */
    public function reject(Order $order)
    {
        if (in_array($order->status, ['pending', 'approved'])) {
            $order->update(['status' => 'cancelled']);
            
            // Refund user if balance was used
            if ($order->balance_used > 0) {
                $user = $order->user;
                $user->increment('balance', $order->balance_used);
                
                $user->transactions()->create([
                    'amount' => $order->balance_used,
                    'type' => 'refund',
                    'description' => '#' . $order->id . ' nolu sipariş iptal iadesi.',
                    'order_id' => $order->id
                ]);
            }

            return redirect()->back()->with('success', 'Sipariş iptal edildi.');
        }

        return redirect()->back()->with('error', 'Sipariş iptal edilemez.');
    }

    private function getStepDescription(string $step): string
    {
        return match($step) {
            'preparing' => 'Siparişiniz hazırlanıyor.',
            'shipped' => 'Siparişiniz kargoya verildi.',
            'delivered' => 'Siparişiniz teslim edildi.',
            'completed' => 'Siparişiniz tamamlandı.',
            default => 'Sipariş süreci devam ediyor.',
        };
    }
}
