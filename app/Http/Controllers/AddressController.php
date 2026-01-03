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
        public function pagination(Request $request): JsonResponse
    {

        $addresses = Address::query() // 1. Bilow query-ga
            ->with('user:id,name,email') // 2. Eager load user data
            
            // 3. Codso filters-ka si xikmad leh adigoo isticmaalaya "when()"
            ->when($request->country, fn($query, $country) => $query->country($country))
            ->when($request->status, fn($query, $status) => $query->status($status))
            ->when($request->search, fn($query, $terms) => $query->search($terms))
            
            ->latest() // 4. Kala hormari kuwii ugu dambeeyay
            ->paginate(15); // 5. Halkan ku samee pagination-ka (15 xariiq boggiiba)

        return response()->json([
            'success' => true,
            'data' => $addresses // Laravel wuxuu siinayaa metadata pagination
        ]);
    }
public function filters(Request $request): JsonResponse
{
    $request->validate([
        'status' => 'nullable|in:draft,canceled,submitted',
        'country' => 'nullable|string',
        'search' => 'nullable|string',
    ]);

    $addresses = Address::query()
        ->with('user:id,name,email')
        ->when($request->country, fn ($q, $country) => $q->country($country))
        ->when($request->status, fn ($q, $status) => $q->status($status))
        ->when($request->search, fn ($q, $terms) => $q->search($terms))
        ->latest()
        ->paginate(10);

    return response()->json([
        'success' => true,
        'data' => $addresses
    ]);
}

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
