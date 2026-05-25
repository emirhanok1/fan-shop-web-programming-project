<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index()
    {
        // To be implemented
    }

    /**
     * Display details of a specific order.
     */
    public function show(Order $order)
    {
        // To be implemented
    }

    /**
     * Cancel a pending order (refunds to balance).
     */
    public function cancel(Order $order)
    {
        // To be implemented
    }

    /**
     * Confirm delivery of an order.
     */
    public function confirm(Order $order)
    {
        // To be implemented
    }
}
