<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Core\Traits\AuditTrailTraits;
use App\Core\Traits\GlobalTraits;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    use AuditTrailTraits, GlobalTraits;

    public function list()
    {
        return User::with(['auditTrails'])->get();
    }

    public function pagination(Request $request)
    {
        $query = User::with('auditTrails')->orderBy('id', 'desc');
        return $this->paginate($query, $request);
    }

    public function filtration(Request $request)
    {
        return $this->filter(User::with('auditTrails'), $request->condition, $request);
    }

    public function save(Request $request)
    {
       return DB::transaction(function () use ($request) {
            $validData = $this->validateData($request);
            $validData['status'] = 'draft';
            $user = User::create($validData);
            $this->auditTrail('save', $user->id, now(), 'user', 'Created');
            return response()->json([
                User::with(['auditTrails', 'role'])->where('id', $user['id'])->first()
            ]);
        });
    }


    public function show(User $user)
    {
        return response()->json([
            User::with(['auditTrails', 'role'])->where('id', $user->id)->first()
        ]);
    }

    public function update(Request $request, User $user)
    {
        return DB::transaction(function () use ($request, $user) {
            $validateData = $this->validateData($request, $user->id);
            $user->update($validateData);
            $this->auditTrail('update', $user->id, now(), 'user', 'Updated');
            return response()->json([
                User::with(['auditTrails', 'role'])->where('id', $user['id'])->first()
            ]);
        });
    }



    public function submit(User $user)
    {
        return DB::transaction(function () use ($user) {
            $user->update(['status' => 'submitted']);
            $this->auditTrail('submit', $user->id, now(), 'user', 'Submitted user');
            return response()->json([
                User::with(['auditTrails', 'role'])->where('id', $user['id'])->first()
            ]);
        });
    }



    public function cancel(User $user)
    {
        return DB::transaction(function () use ($user) {
            $user->update(['status' => 'draft']);

            $this->auditTrail('cancel', $user->id, now(), 'user', 'Cancelled user');

            return response()->json([
                User::with(['auditTrails', 'role'])->where('id', $user['id'])->first()
            ]);
        });
    }

  

    public function delete(User $user)
    {
        $this->auditTrail('delete', $user['id'], now(), 'user', 'user deleted');
        return $user->delete();
    }

    protected function validateData(Request $request, $userId = null)
    {
        // Get user ID from request or parameter (for updates)
        $id = $userId ?? $request->id ?? 'NULL';
        
        $rules = [
            'fullname' => 'nullable|string|max:255',
            'email' => "required|email|unique:users,email,{$id},id",
            'phone' => 'nullable|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'is_blocked' => 'nullable|boolean',
        ];

        // Add password validation if provided
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:6';
        }

        // Validate the request and get validated data
        $validated = $request->validate($rules);

        // Remove confirm_password from validated data as it's not stored
        unset($validated['confirm_password']);

        return $validated;
    }
}
