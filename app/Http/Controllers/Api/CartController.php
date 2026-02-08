<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private const USER_ID = 1;

    public function index(Request $request): JsonResponse
    {
        try {
            $userId = (int) $request->query('user_id', self::USER_ID);

            $carts = Cart::with(['product.images'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            $items = $carts->map(function ($cart) {
                return [
                    'id' => $cart->id,
                    'user_id' => $cart->user_id,
                    'product_id' => $cart->product_id,
                    'product_name' => $cart->product->name ?? null,
                    'product_price' => (float) $cart->price,
                    'quantity' => $cart->quantity,
                    'total' => (float) $cart->total,
                    'product_images' => $cart->product && $cart->product->images
                        ? $cart->product->images->map(fn ($img) => [
                            'id' => $img->id,
                            'url' => asset('storage/' . $img->image),
                        ])->values()
                        : [],
                ];
            });

            $cart_total = round($carts->sum('total'), 2);
            $items_count = $carts->sum('quantity');

            return response()->json([
                'success' => true,
                'user_id' => $userId,
                'data' => $items,
                'cart_total' => $cart_total,
                'items_count' => $items_count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cart.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function show(int $userId): JsonResponse
    {
        try {
            $carts = Cart::with(['product.images'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            $items = $carts->map(function ($cart) {
                return [
                    'id' => $cart->id,
                    'user_id' => $cart->user_id,
                    'product_id' => $cart->product_id,
                    'product_name' => $cart->product->name ?? null,
                    'product_price' => (float) $cart->price,
                    'quantity' => $cart->quantity,
                    'total' => (float) $cart->total,
                    'product_images' => $cart->product && $cart->product->images
                        ? $cart->product->images->map(fn ($img) => ['id' => $img->id, 'url' => asset('storage/' . $img->image)])->values()
                        : [],
                ];
            });

            return response()->json([
                'success' => true,
                'user_id' => $userId,
                'data' => $items,
                'cart_total' => round($carts->sum('total'), 2),
                'items_count' => $carts->sum('quantity'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cart.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $product = Product::findOrFail($validated['product_id']);
            $quantity = (int) $validated['quantity'];
            $price = (float) $product->price;
            $total = round($price * $quantity, 2);

            $cart = Cart::create([
                'user_id' => self::USER_ID,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $total,
            ]);

            $cart->load('product.images');

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart.',
                'data' => [
                    'id' => $cart->id,
                    'user_id' => $cart->user_id,
                    'product_id' => $cart->product_id,
                    'product_name' => $cart->product->name,
                    'quantity' => $cart->quantity,
                    'price' => (float) $cart->price,
                    'total' => (float) $cart->total,
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
                'message' => 'Failed to add to cart.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function update(Request $request, string|int $id): JsonResponse
    {
        $id = (int) $id;
        $userId = (int) $request->input('user_id', $request->query('user_id', self::USER_ID));

        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            $cart = Cart::where('id', $id)->where('user_id', $userId)->firstOrFail();
            $cart->quantity = (int) $validated['quantity'];
            $cart->total = round($cart->price * $cart->quantity, 2);
            $cart->save();

            return response()->json([
                'success' => true,
                'message' => 'Cart item updated.',
                'data' => [
                    'id' => $cart->id,
                    'quantity' => $cart->quantity,
                    'total' => (float) $cart->total,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Cart item not found.'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function destroy(Request $request, string|int $id): JsonResponse
    {
        $id = (int) $id;
        $userId = (int) $request->input('user_id', $request->query('user_id', self::USER_ID));

        try {
            $cart = Cart::where('id', $id)->where('user_id', $userId)->firstOrFail();
            $cart->delete();
            return response()->json([
                'success' => true,
                'message' => 'Cart item removed.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Cart item not found.'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove cart item.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
