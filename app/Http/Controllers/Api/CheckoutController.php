<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    private const USER_ID = 1;

    /**
     * Checkout: create order from cart and optionally create Stripe PaymentIntent.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $carts = Cart::with('product')->where('user_id', self::USER_ID)->get();

            if ($carts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty.',
                ], 422);
            }

            $subtotal = round($carts->sum('total'), 2);
            $tax = 0;
            $total = round($subtotal + $tax, 2);
            $orderNumber = 'ORD-' . strtoupper(Str::random(8));

            $order = Order::create([
                'user_id' => self::USER_ID,
                'order_number' => $orderNumber,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $request->input('payment_method', 'stripe'),
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

            $paymentIntent = null;
            $stripeSecret = config('services.stripe.secret');
            if (!empty($stripeSecret) && class_exists(\Stripe\Stripe::class)) {
                try {
                    \Stripe\Stripe::setApiKey($stripeSecret);
                    $pi = \Stripe\PaymentIntent::create([
                        'amount' => (int) round($total * 100), // cents
                        'currency' => $request->input('currency', 'inr'),
                        'metadata' => ['order_id' => $order->id, 'order_number' => $orderNumber],
                        'automatic_payment_methods' => ['enabled' => true],
                    ]);
                    $order->update(['payment_id' => $pi->id]);
                    $paymentIntent = ['client_secret' => $pi->client_secret, 'id' => $pi->id];
                } catch (\Exception $e) {
                    // Log but don't fail order creation
                    $paymentIntent = ['error' => 'Payment intent creation failed.', 'message' => $e->getMessage()];
                }
            }

            Cart::where('user_id', self::USER_ID)->delete();

            $order->load('items.product');

            return response()->json([
                'success' => true,
                'message' => 'Order created. Complete payment to confirm.',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'subtotal' => (float) $order->subtotal,
                    'tax' => (float) $order->tax,
                    'total' => (float) $order->total,
                    'status' => $order->status,
                    'payment_intent' => $paymentIntent,
                    'items' => $order->items->map(fn ($i) => [
                        'product_id' => $i->product_id,
                        'product_name' => $i->product->name ?? null,
                        'quantity' => $i->quantity,
                        'price' => (float) $i->price,
                        'total' => (float) $i->total,
                    ]),
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Checkout failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
