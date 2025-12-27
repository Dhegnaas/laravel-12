<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function list()
    {
        $users = User::all();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user = User::find($user->id);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }



    /**
     * Register a new user
     */
    public function save(Request $request)
    {


        return DB::transaction(function () use ($request) {

            $validateData = $this->validateUserData($request);

            $user = User::create($validateData);

            return response()->json([
                'user' => $user,
                'message' => 'User registered successfully',
            ], 201);


        });
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {

        return DB::transaction(function () use ($request, $user) {

            $validateData = $this->validateUserData($request, $user);

            $user->update($validateData);

            $user = User::find($user->id);

            return response()->json([
                'data' => $user,
                'message' => 'User updated successfully',
            ]);

        });
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user, $id)
    {
        $user = User::find($user->id);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Update the authenticated user's profile
     */
    public function updateProfile(Request $request, User $user, $id)
    {


        return DB::transaction(function () use ($request, $user, $id) {
            $user = User::find($id);
            
            $validateData = $this->validateUserData($request, $user);

            $user->update($validateData);


            return response()->json([
                'data' => $user,
                'message' => 'Profile updated successfully',
            ]);

        });
    }

    /**
     * Delete the authenticated user's account
     */
    public function deleteProfile(Request $request, User $user, $id)
    {
        $user = User::find($user->id);
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully',
        ]);
    }




    protected function validateUserData(Request $request, User $user = null)
    {
        // Build validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255'],
        ];

        // Password validation: required for new users, optional for updates
        if ($user) {
            // For updates, password is optional
            $rules['password'] = 'sometimes|string|min:4|confirmed';
        } else {
            // For new users, password is required
            $rules['password'] = 'required|string|min:4|confirmed';
        }

        // For updates, ignore current user's email in uniqueness check
        if ($user) {
            $rules['email'][] = Rule::unique('users')->ignore($user->id);
        } else {
            // For new users, email must be unique
            $rules['email'][] = 'unique:users';
        }

        // Validate the request and get validated data
        $validated = $request->validate($rules);

        // Hash the password if provided
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Remove password_confirmation as it's only for validation
        unset($validated['password_confirmation']);

        return $validated;
    }
}
