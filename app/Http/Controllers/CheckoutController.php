<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    private const DEFAULT_USER_ID = 1;

    
    public function store(Request $request)
    {
        $userId = auth()->id() ?? self::DEFAULT_USER_ID;

        $carts = Cart::with('product')->where('user_id', $userId)->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = round($carts->sum('total'), 2);
        $tax = 0;
        $total = round($subtotal + $tax, 2);
        $orderNumber = 'ORD-' . strtoupper(Str::random(8));

        $order = Order::create([
            'user_id' => $userId,
            'order_number' => $orderNumber,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'status' => 'pending',
            'payment_method' => 'web',
        ]);

        foreach ($carts as $cart) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
                'price' => $cart->price,
                'total' => $cart->total,
            ]);
        }

        Cart::where('user_id', $userId)->delete();

        return redirect()->route('checkout.success', $order)
            ->with('success', 'Order placed successfully.');
    }

    public function success(Order $order)
    {
        $userId = auth()->id() ?? self::DEFAULT_USER_ID;
        if ((int) $order->user_id !== (int) $userId) {
            abort(403, 'This order does not belong to you.');
        }
        $order->load('items.product');
        return view('checkout.success', compact('order'));
    }
}
