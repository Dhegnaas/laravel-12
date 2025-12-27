<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('user')->get();
        
        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateProductData($request);

        $product = Product::create([
            ...$validatedData,
            'user_id' => $request->user()->id,
        ]);

        $product->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('user');
        
        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Check if user owns the product
        $unauthorized = $this->authorizeProductAccess($request, $product);
        if ($unauthorized) {
            return $unauthorized;
        }

        $validatedData = $this->validateProductData($request, true);
        $product->update($validatedData);
        $product->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product)
    {
        // Check if user owns the product
        $unauthorized = $this->authorizeProductAccess($request, $product);
        if ($unauthorized) {
            return $unauthorized;
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    /**
     * Validate product data for create/update operations
     *
     * @param Request $request
     * @param bool $isUpdate Whether this is an update operation (uses 'sometimes' rules)
     * @return array Validated data ready for database operations
     */
    private function validateProductData(Request $request, bool $isUpdate = false): array
    {
        $rules = [
            'name' => ($isUpdate ? 'sometimes|' : '') . 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => ($isUpdate ? 'sometimes|' : '') . 'required|numeric|min:0',
            'featured_image' => 'nullable|string',
            'featured_image_organizational_name' => 'nullable|string',
        ];

        $validated = $request->validate($rules);

        return $request->only([
            'name',
            'description',
            'price',
            'featured_image',
            'featured_image_organizational_name',
        ]);
    }

    /**
     * Check if the authenticated user owns the product
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse|null Returns error response if unauthorized, null if authorized
     */
    private function authorizeProductAccess(Request $request, Product $product)
    {
        if ($product->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only modify your own products.',
            ], 403);
        }

        return null;
    }
}
