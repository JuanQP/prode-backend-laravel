<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['token', 'register']]);
    }

    public function token(LoginRequest $request)
    {
        $token = Auth::attempt($request->validated());
        if (!$token) {
            return response()->json([
                'message' => 'Incorrect credentials',
            ], 401);
        }

        return response()->json([
            'refresh' => '',
            'access' => $token,
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->safe()->except(['password2']);
        $validated['password'] = Hash::make($request->password);
        $user = User::create($validated);

        return response()->json([
            'username' => $user->username,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'avatar' => '',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'access' => Auth::refresh(),
        ]);
    }

    public function me()
    {
        return response()->json([
            'message' => 'You are seeing this response because you are logged in.',
        ]);
    }
}
