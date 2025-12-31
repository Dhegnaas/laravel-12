<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    /**
     * Tusi dhammaan cinwaannada (Latest First + Pagination)
     */
    public function index(): JsonResponse
    {
        $addresses = Address::with('user:id,name,email')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }
    /**
     * Display the specified resource (hal cinwaan oo gaar ah).
     */
    public function show(Address $address): JsonResponse
    {
        // Waxay soo celinaysaa cinwaanka la codsaday, oo ay la socoto xogta qofka iska leh (user)
        return response()->json([
            'success' => true,
            'data' => $address->load('user:id,name,email'),
        ]);
    }

    /**
     * Kaydi cinwaan cusub (Default Status: Draft)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateRequest($request);

        return DB::transaction(function () use ($validated) {
            // Halkan ayaan si sax ah u isticmaalnay relationship-ka addresses()
            $address = Auth::user()->addresses()->create([
                ...$validated,
                'status' => 'draft'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Address created as draft',
                'data' => $address->load('user:id,name')
            ], 201);
        });
    }

    /**
     * Cusboonaysii cinwaanka (Cidda iska leh kaliya)
     */
    public function update(Request $request, Address $address): JsonResponse
    {
        $this->authorizeOwner($address);

        $validated = $this->validateRequest($request, true);

        $address->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data' => $address->refresh()
        ]);
    }

    /**
     * Status Change: Submit
     */
    public function submit(Address $address): JsonResponse
    {
        return $this->updateStatus($address, 'submitted');
    }

    /**
     * Status Change: Cancel
     */
    public function cancel(Address $address): JsonResponse
    {
        return $this->updateStatus($address, 'canceled');
    }

    /**
     * Tirtirid (Cidda iska leh kaliya)
     */
    public function destroy(Address $address): JsonResponse
    {
        $this->authorizeOwner($address);
        
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

    // --- HELPER METHODS ---

    /**
     * Hubinta Amniga: User-ka ma iska leh xogtan?
     */
    private function authorizeOwner(Address $address): void
    {
        if ($address->user_id !== Auth::id()) {
            abort(response()->json(['message' => 'Unauthorized Access'], 403));
        }
    }

    /**
     * Hubinta Xogta (Validation)
     */
    private function validateRequest(Request $request, bool $isUpdate = false): array
    {
        $rules = [
            'country'  => ($isUpdate ? 'sometimes|' : 'required|') . 'string|max:255',
            'district' => ($isUpdate ? 'sometimes|' : 'required|') . 'string|max:255',
            'location' => ($isUpdate ? 'sometimes|' : 'required|') . 'string|max:255',
            'area'     => 'nullable|string|max:255',
            'status'   => ['sometimes', Rule::in(['draft', 'submitted', 'canceled'])]
        ];

        return $request->validate($rules);
    }

    /**
     * Beddelidda Status-ka
     */
    private function updateStatus(Address $address, string $status): JsonResponse
    {
        $this->authorizeOwner($address);

        $address->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'message' => "Address is now $status",
            'status' => $address->status
        ]);
    }
}
