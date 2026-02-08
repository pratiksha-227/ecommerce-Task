<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * GET API - Display all products with multiple images.
     */
    public function index(): JsonResponse
    {
        try {
            $products = Product::with('images')->get()->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'description' => $product->description,
                    'quantity' => $product->quantity,
                    'images' => $product->images->map(fn ($img) => [
                        'id' => $img->id,
                        'url' => asset('storage/' . $img->image),
                        'path' => $img->image,
                    ]),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $products,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
