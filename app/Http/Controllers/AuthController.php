<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{

    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }
 
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

       
        $user = User::where('email', $request->email)->first();

        
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->is_blocked) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been blocked. Please contact support.'],
            ]);
        }

        
        if ($user->status !== 'submitted') {
            throw ValidationException::withMessages([
                'email' => ['Your account is not active. Please wait for approval.'],
            ]);
        }

        
        Auth::login($user);
        $token = $user->createToken('token')->plainTextToken;
        $cookies = Cookie::make('jwt', $token);
        session([
            'login_time' => now(),
            'last_activity' => now()
        ]);
        
        return response()->json([
            'code' => 200,
            'message' => 'Authentication successful.',
            'token' => $token
        ], 200)->withCookie($cookies);
    }

   
    public function logout(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
          
            PersonalAccessToken::where('tokenable_id', $user->id)
                ->where('tokenable_type', 'App\Models\User')
                ->delete();

           
            Session::flush();

           
            $cookie = Cookie::forget('jwt');


            return response()->json(['message' => 'Logged out successfully.'], 200)->withCookie($cookie);
            
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Logout failed.',
                'error' => $th->getMessage()
            ], 422);
        }
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }
}
