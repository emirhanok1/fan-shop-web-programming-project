<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of all orders.
     */
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        // Status filtresi
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Arama (invoice_no veya kullanıcı adı/email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15)->withQueryString();
        
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
            DB::beginTransaction();
            try {
                $order->update(['status' => 'approved']);
                
                // Initialize tracking at step 1 if not exists (DB enum default is 'sourcing')
                if (!$order->tracking) {
                    $order->tracking()->create([
                        'step' => 'sourcing',
                    ]);
                }

                DB::commit();
                return redirect()->back()->with('success', 'Sipariş onaylandı.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Sipariş onaylanırken hata oluştu: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Sipariş onaylanamaz.');
    }

    /**
     * Advance the tracking step of an approved order.
     */
    public function advance(Order $order)
    {
        if ($order->tracking && $order->status === 'approved') {
            $steps = ['sourcing', 'packaging', 'shipped', 'on_the_way', 'delivered'];
            $currentStep = $order->tracking->step;
            $currentIndex = array_search($currentStep, $steps);

            if ($currentIndex !== false && $currentIndex < count($steps) - 1) {
                $nextStep = $steps[$currentIndex + 1];
                
                $order->tracking->update([
                    'step' => $nextStep,
                ]);

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
            DB::beginTransaction();
            try {
                $order->update(['status' => 'cancelled']);
                
                // Restore product stocks
                foreach ($order->items as $item) {
                    $item->product->increment('stock', $item->quantity);
                }

                // Refund the entire total_amount to balance (credit card is not refunded)
                $user = $order->user;
                $user->increment('balance', $order->total_amount);
                
                $user->transactions()->create([
                    'amount' => $order->total_amount,
                    'type' => 'refund',
                    'description' => '#' . $order->id . ' nolu sipariş iptal iadesi.',
                    'order_id' => $order->id
                ]);

                DB::commit();
                return redirect()->back()->with('success', 'Sipariş iptal edildi ve tutar kullanıcının cüzdanına iade edildi.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Sipariş iptal edilirken hata oluştu: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Sipariş iptal edilemez.');
    }
}
