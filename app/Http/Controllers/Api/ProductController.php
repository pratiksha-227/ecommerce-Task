<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private function formatProduct(Product $product): array
    {
        $product->load('images');
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'description' => $product->description,
            'quantity' => (int) $product->quantity,
            'images' => $product->images->map(fn ($img) => [
                'id' => $img->id,
                'url' => asset('storage/' . $img->image),
                'path' => $img->image,
            ])->values()->all(),
        ];
    }

    /**
     * GET API - List all products with images.
     */
    public function index(): JsonResponse
    {
        try {
            $products = Product::with('images')->get()->map(fn ($p) => $this->formatProduct($p));
            return response()->json(['success' => true, 'data' => $products]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET API - Single product with images.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = Product::with('images')->findOrFail($id);
            return response()->json(['success' => true, 'data' => $this->formatProduct($product)]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Collect uploaded image(s). Accepts "image" (single file) or "images" / "images[]" (array).
     */
    private function getUploadedImages(Request $request): array
    {
        if ($request->hasFile('image')) {
            return [$request->file('image')];
        }
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            return is_array($files) ? $files : [$files];
        }
        return [];
    }

    /**
     * POST API - Create product. Body: form-data (name, price, quantity?, description?, image or images[]).
     * Or JSON (name, price, quantity?, description?) without images.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'quantity' => 'nullable|integer|min:0',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'images' => 'nullable|array',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $product = Product::create([
                'name' => trim($request->name),
                'price' => (float) $request->price,
                'quantity' => (int) ($request->quantity ?? 0),
                'description' => $request->description ? trim($request->description) : null,
            ]);

            foreach ($this->getUploadedImages($request) as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create(['product_id' => $product->id, 'image' => $path]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data' => $this->formatProduct($product->fresh()),
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
                'message' => 'Failed to create product.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    
    public function update(Request $request, string|int $id): JsonResponse
    {
        $id = (int) $id;
        if ($id <= 0) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }
        try {
            $product = Product::findOrFail($id);

            // Debug: Log what we're receiving
            \Log::info('Update request received', [
                'method' => $request->method(),
                'content_type' => $request->header('Content-Type'),
                'all_input' => $request->all(),
                'has_file_image' => $request->hasFile('image'),
                'has_file_images' => $request->hasFile('images'),
                'raw_content' => substr($request->getContent(), 0, 500), // First 500 chars
            ]);

            // Check if request body is empty (common with PUT/PATCH + form-data)
            $isMultipart = str_contains($request->header('Content-Type', ''), 'multipart/form-data');
            if (in_array($request->method(), ['PUT', 'PATCH']) && $isMultipart && empty($request->all()) && empty($request->allFiles())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request body is empty. PHP does not parse PUT/PATCH request bodies with multipart/form-data.',
                    'error' => 'For form-data (with or without file), you MUST use POST method instead of ' . $request->method() . '.',
                    'hint' => 'Change method to POST: POST /api/products/' . $id,
                    'received_method' => $request->method(),
                    'content_type' => $request->header('Content-Type'),
                ], 422);
            }

            $request->validate([
                'name' => 'nullable|string|max:255',
                'price' => 'nullable|numeric|min:0',
                'quantity' => 'nullable|integer|min:0',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'images' => 'nullable|array',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $updateData = [];
            if ($request->has('name')) {
                $updateData['name'] = trim($request->name);
            }
            if ($request->has('price')) {
                $updateData['price'] = (float) $request->price;
            }
            if ($request->has('quantity')) {
                $updateData['quantity'] = (int) $request->quantity;
            }
            if ($request->has('description')) {
                $updateData['description'] = trim($request->description);
            }

            // If multipart/form-data but no data parsed, it means PUT/PATCH was used incorrectly
            if ($isMultipart && empty($updateData) && empty($request->allFiles()) && !empty($request->getContent())) {
                return response()->json([
                    'success' => false,
                    'message' => 'No form data was parsed from the request body.',
                    'error' => 'PHP does not parse PUT/PATCH request bodies. You are using ' . $request->method() . ' with multipart/form-data.',
                    'solution' => 'Change the HTTP method to POST in your API client (Postman). Use: POST /api/products/' . $id,
                    'received_method' => $request->method(),
                ], 422);
            }

            if (!empty($updateData)) {
                $product->update($updateData);
            }

            foreach ($this->getUploadedImages($request) as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create(['product_id' => $product->id, 'image' => $path]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'data' => $this->formatProduct($product->fresh()),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * DELETE API - Delete product and all its images from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $product = Product::with('images')->findOrFail($id);
            foreach ($product->images as $img) {
                Storage::disk('public')->delete($img->image);
            }
            $product->delete();
            return response()->json(['success' => true, 'message' => 'Product deleted.']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * DELETE API - Remove one image from a product.
     */
    public function destroyImage(int $productId, int $imageId): JsonResponse
    {
        try {
            $image = ProductImage::where('product_id', $productId)->where('id', $imageId)->firstOrFail();
            Storage::disk('public')->delete($image->image);
            $image->delete();
            return response()->json(['success' => true, 'message' => 'Image removed.']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Image or product not found.'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove image.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
