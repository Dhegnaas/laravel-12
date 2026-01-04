<?php

namespace App\Http\Controllers\auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait AuthTrait
{
    public function authenticateUser(User $user, $verify = false)
    {
            Auth::login($user);
            return response()->json([
                'user' => $user,
            ]);
        
    }
}
