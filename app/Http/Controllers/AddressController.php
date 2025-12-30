<?php

namespace App\Http\Controllers;

use App\Models\Address;
use DB;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $address = Address::with('user')->get();
        return response()->json([
            'success' => true,
            'data' => $address,
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
        $validatedData = $this->validateAddressData($request);

        $address = Address::create([
            ...$validatedData,
            'user_id' => $request->user()->id,
        ]);

        $address->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Address created successfully',
            'data' => $address,
        ], 201);   
    }); 
    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address)
    {
        $address = Address::find($address->id);
        $address->load('user');

        return response()->json([
            'success' => true,
            'data' => $address,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Address $address)
    {

        $authorizationResponse = $this->authorizeAddressAccess($request, $address);
        if ($authorizationResponse) {
            return $authorizationResponse;
        }   

        return DB::transaction(function () use ($request, $address) {

        $validatedData = $this->validateAddressData($request, true);

        $address->update($validatedData);

        $address->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data' => $address,
        ]);
    }); 
    } 


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        $authorizationResponse = $this->authorizeAddressAccess(request(), $address);
        if ($authorizationResponse) {
            return $authorizationResponse;
        }
        $address = Address::find($address->id);
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully',
        ]);
    }
    private function validateAddressData(Request $request, bool $isUpdate = false): array
    {
        $rules = [
            'country' => ($isUpdate ? 'sometimes|' : '') . 'required|string|max:255',
            'district' => ($isUpdate ? 'sometimes|' : '') . 'required|string|max:255',
            'location' => ($isUpdate ? 'sometimes|' : '') . 'required|string|max:255',
            'area' => ($isUpdate ? 'sometimes|' : '') . 'nullable|string',
        ];

        $validated = $request->validate($rules);

        return $request->only([
            'country',
            'district',
            'location',
            'area',
        ]);
    }

        private function authorizeAddressAccess(Request $request, Address $address)
    {
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only modify your own address',
            ], 403);
        }

        return null;
    }

}
