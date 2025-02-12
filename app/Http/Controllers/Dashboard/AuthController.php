<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use App\Http\Requests\admin\login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\admin\register;

class AuthController extends Controller
{
    public function register(register $request)
    {
        if (User::count() > 0) {
            return response()->json(['message' => 'Only one user can be registered'], 403);
        }

        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);
        $admin = User::create($validatedData);
        return response()->json(['message' => 'User registered successfully'], 201);
    }

    // Login user
    public function login(login $request)
    {
        $validatedData = $request->validated();
        $admin = User::where('email', $request->input('email'))->first();
        if (!$admin) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (!Hash::check($request->input('password'), $admin->password)) {
            return response()->json(['message' => 'Incorrect password'], 401);
        }
        $token = $admin->createToken('Api token of ' . $admin->name)->plainTextToken;

        return response()->json([
            'admin' => $admin,
            'token' => $token
        ]);
    }

    // Logout user
    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
