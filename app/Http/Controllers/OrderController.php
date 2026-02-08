<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private const DEFAULT_USER_ID = 1;


    public function index(Request $request)
    {
        $orders = Order::with('user', 'items.product')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'items.product');
        return view('orders.show', compact('order'));
    }

    public function myOrders(Request $request)
    {
        $userId = auth()->id() ?? self::DEFAULT_USER_ID;
        $orders = Order::with('items.product')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.my-index', compact('orders'));
    }

   
    public function myOrderShow(Order $order)
    {
        $userId = auth()->id() ?? self::DEFAULT_USER_ID;
        if ((int) $order->user_id !== (int) $userId) {
            abort(403, 'This order does not belong to you.');
        }
        $order->load('items.product');
        return view('orders.my-show', compact('order'));
    }
}
