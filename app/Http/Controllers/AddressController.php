<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Core\Traits\AuditTrailTraits;
use App\Core\Traits\GlobalTraits;

class AddressController extends Controller
{
    use AuditTrailTraits, GlobalTraits;

    public function list()
    {
        return Address::with(['auditTrails'])->get();
    }

        public function pagination(Request $request)
    {   
        $query = Address::with('auditTrails')->orderBy('id', 'desc');
        return $this->paginate($query, $request);
    }

    public function filtration(Request $request)
    {
        return $this->filter(Address::with('auditTrails'), $request->condition, $request);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function save(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $validData = $this->validateData($request);
            $validData['status'] = 'draft';
            $validData['created_by'] = auth()->id();
            $address = Address::create($validData);
            $this->auditTrail('save', $address->id, now(), 'address', 'Created');
            return response()->json([
                Address::with(['auditTrails'])->where('id', $address->id)->first()
            ]);
        });
    }
    public function show(Address $address)
    {
        return response()->json(
            $address->load(['auditTrails'])
        );
    }   
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Address $address)
    {
        return DB::transaction(function () use ($request, $address) {
            $validateData = $this->validateData($request, $address->id);
            $address->update($validateData);
            $this->auditTrail('update', $address->id, now(), 'address', 'Updated');
            return response()->json([
                Address::with(['auditTrails'])->where('id', $address->id)->first()
            ]);
        });
    }
    public function submit(Request $request, Address $address)
    {
        $address->update(['status' => 'submitted']);
        $this->auditTrail('submit', $address->id, now(), 'address', 'Submitted');
        return response()->json([
            Address::with(['auditTrails'])->where('id', $address->id)->first()
        ]);
    }
    public function cancel(Request $request, Address $address)
    {
        $address->update(['status' => 'canceled']);
        $this->auditTrail('cancel', $address->id, now(), 'address', 'Canceled');
        return response()->json([
            Address::with(['auditTrails'])->where('id', $address->id)->first()
        ]);
    }              
    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, Address $address)
    {
        $this->auditTrail('delete', $address->id, now(), 'address', 'Deleted');
        $address->delete();
        return response()->json(['message' => 'Address deleted successfully.']);
    }
    protected function validateData(Request $request, $id = null)
    {
        $id = $address->id ?? $request->id ?? null;
        $Rules =[
            'country' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'location' => 'required|string|max:100',
            'area' => 'required|string|max:100',
        ];
        return $request->validate($Rules);
        }
}
