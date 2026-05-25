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
        // To be implemented
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // To be implemented
    }

    /**
     * Approve a pending order.
     */
    public function approve(Order $order)
    {
        // To be implemented
    }

    /**
     * Advance the tracking step of an approved order.
     */
    public function advance(Order $order)
    {
        // To be implemented
    }

    /**
     * Reject/Cancel an order from the admin side.
     */
    public function reject(Order $order)
    {
        // To be implemented
    }
}
