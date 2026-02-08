<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
 
    private const DEFAULT_USER_ID = 1;

    
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'product_image_id' => 'nullable|exists:product_images,id',
        ]);

        $userId = auth()->id() ?? self::DEFAULT_USER_ID;
        $product = Product::findOrFail($request->product_id);
        $quantity = (int) ($request->quantity ?? 1);
        $price = (float) $product->price;
        $total = $price * $quantity;

        $productImageId = null;
        if ($request->filled('product_image_id')) {
            $img = \App\Models\ProductImage::where('id', $request->product_image_id)->where('product_id', $product->id)->first();
            if ($img) {
                $productImageId = $img->id;
            }
        }

        Cart::create([
            'user_id' => $userId,
            'product_id' => $product->id,
            'product_image_id' => $productImageId,
            'quantity' => $quantity,
            'price' => $price,
            'total' => $total,
        ]);

        return redirect()->back()->with('success', '"' . $product->name . '" added to cart.');
    }

   
    public function index(Request $request)
    {
        $userId = $request->query('user_id', auth()->id() ?? self::DEFAULT_USER_ID);
        $items = Cart::with(['product.images', 'selectedImage'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cart.index', compact('items', 'userId'));
    }

   
    public function remove(Request $request, $id)
    {
        $userId = auth()->id() ?? self::DEFAULT_USER_ID;
        $item = Cart::where('id', $id)->where('user_id', $userId)->firstOrFail();
        $item->delete();
        return redirect()->route('cart.index', $request->only('user_id'))->with('success', 'Item removed from cart.');
    }
}
