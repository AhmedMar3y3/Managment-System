<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\login;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\admin\register;

class AuthController extends Controller
{
    public function register(register $request)
    {
        if (User::count() > 0) {
            return response()->json(['message' => 'لا يمكن تسجيل أكثر من مستخدم واحد'], 403);
        }

        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);
        $admin = User::create($validatedData);
        return response()->json(['message' => 'تم تسجيل المستخدم بنجاح'], 201);
    }

    // Login user
    public function login(login $request)
    {
        $validatedData = $request->validated();
        $admin = User::where('email', $request->input('email'))->first();
        if (!$admin) {
            return response()->json(['message' => 'المستخدم غير موجود'], 404);
        }

        if (!Hash::check($request->input('password'), $admin->password)) {
            return response()->json(['message' => 'كلمة المرور غير صحيحة'], 401);
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
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح'], 200);
    }
}
