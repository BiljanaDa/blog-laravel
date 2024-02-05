<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignIn;
use App\Http\Requests\SignUp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signup(SignUp $request)
    {
        $request->validated();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => "Registration successful"
        ]);
    }

    public function signin(SignIn $request)
    {
        $request->validated();

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => "Invalid credentials"
            ], 401);
        }

        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout() {
        Auth::user()->tokens->delete();
        return response()->json([
         'message' => 'Successfully logged out'
        ]);
    }
}
