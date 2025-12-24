<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function list()
    {
        $users = User::with('products')->get();
        
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
        $user->load('products');
        
        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $this->validateUserData($request, $user->id);

        $user->update($validatedData);
        $user->load('products');

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user,
        ]);
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Update the authenticated user's profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $validatedData = $this->validateUserData($request, $user->id);

        $user->update($validatedData);
        $user->load('products');

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user,
        ]);
    }

    /**
     * Delete the authenticated user's account
     */
    public function deleteProfile(Request $request)
    {
        $user = $request->user();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully',
        ]);
    }

    /**
     * Validate user data for update operations
     *
     * @param Request $request
     * @param int|null $userId User ID to ignore in email uniqueness check
     * @return array Validated and processed data ready for database update
     */
    private function validateUserData(Request $request, ?int $userId = null): array
    {
        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
            ],
            'password' => 'sometimes|required|string|min:4|confirmed',
            'password_confirmation' => 'required_with:password|string|min:4',
        ];

        // Add email uniqueness rule if user ID is provided
        if ($userId !== null) {
            $rules['email'][] = Rule::unique('users')->ignore($userId);
        } else {
            $rules['email'][] = Rule::unique('users');
        }

        $validated = $request->validate($rules);

        // Prepare data for update (exclude password_confirmation)
        $data = $request->only(['name', 'email']);

        // Hash password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        return $data;
    }
}
