<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    /**
     * Logout the authenticated user and revoke tokens.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Handle user login with LoginRequest.
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt([
            filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username' => $request->email,
            'password' => $request->password
        ])) return response()->json(['message' => 'Invalid credentials'], 401);

        $user = Auth::user();
        $user->setAttribute('token', $user->createToken('access_token')->plainTextToken);

        return response()->json([
            'message' => 'Login successful',
            'data' => $user
        ]);
    }
}
