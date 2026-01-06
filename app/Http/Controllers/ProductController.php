<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Core\Traits\AuditTrailTraits;
use App\Core\Traits\GlobalTraits;

class ProductController extends Controller
{
    use AuditTrailTraits, GlobalTraits;
    public function index()
    {
        return Product::with(['auditTrails'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $validData = $this->validateData($request);
            $validData['status'] = 'draft';
            $validData['created_by'] = auth()->id();
            $product = Product::create($validData);
            $this->auditTrail('save', $product->id, now(), 'product', 'Created');
            return response()->json([
                Product::with(['auditTrails'])->where('id', $product->id)->first()
            ]);
        });
    }
    public function pagination(Request $request)
    {
        $query = Product::with('auditTrails')->orderBy('id', 'desc');
        return $this->paginate($query, $request);
    }
    public function filtration(Request $request)
    {
        return $this->filter(Product::with('auditTrails'), $request->condition, $request);
    }
    public function show(Product $product)
    {
        return response()->json(
            $product->load(['auditTrails'])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        return DB::transaction(function () use ($request, $product) {
            $validateData = $this->validateData($request, $product->id);
            $product->update($validateData);
            $this->auditTrail('update', $product->id, now(), 'product', 'Updated');
            return response()->json([
                Product::with(['auditTrails'])->where('id', $product->id)->first()
            ]);
        });
    }

    public function submit(Request $request, Product $product)
    {
        return DB::transaction(function () use ($request, $product) {
            $product->update(['status' => 'submitted']);
            $this->auditTrail('submit', $product->id, now(), 'product', 'Submitted');
            return response()->json([
                Product::with(['auditTrails'])->where('id', $product->id)->first()
            ]);
        });
    }
    public function cancel(Request $request, Product $product)
    {
        return DB::transaction(function () use ($request, $product) {
            $product->update(['status' => 'canceled']);
            $this->auditTrail('cancel', $product->id, now(), 'product', 'Canceled');
            return response()->json([
                Product::with(['auditTrails'])->where('id', $product->id)->first()
            ]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product)
    {
        $this->auditTrail('delete', $product->id, now(), 'product', 'Deleted');
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully.']);
    }
    protected function validateData(Request $request, $id = null)
    {
        $id = $product->id ?? $request->id ?? null;
        $rules = [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ];
        return $request->validate($rules);
    }
}
