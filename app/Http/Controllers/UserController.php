<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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
}
