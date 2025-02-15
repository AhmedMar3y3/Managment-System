<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Http\Requests\chef\login;
use App\Http\Requests\chef\register;
use Illuminate\Http\Request;
use App\Models\Chef;
use App\Models\Manager;
use App\Notifications\Manager\NewEmployee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyPhone;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    // Register a new user
    public function register(register $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('users'), $imageName);
            $validatedData['image'] = env('APP_URL') . '/public/users/' . $imageName;
        }
        $chef = Chef::create($validatedData);

        $verificationCode = mt_rand(1000, 9999);
        $chef->verification_code = $verificationCode;
        $chef->save();

        if ($request->has('email')) {
            Mail::raw("Your verification code is: $verificationCode", function ($message) use ($chef) {
                $message->to($chef->email)->subject('Verification Code');
            });
            return response()->json(['message' => 'Verification code sent to your email'], 201);
        } elseif ($request->has('phone')) {
            Notification::send($chef, new VerifyPhone($verificationCode));
            return response()->json(['message' => 'Verification code sent to your phone'], 201);
        }

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    // Verify account (email or phone)
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $chef = Chef::where('verification_code', $request->code)->first();

        if (!$chef) {
            return response()->json(['message' => 'Invalid verification code'], 404);
        }

        $chef->verified_at = now();
        $chef->verification_code = null;
        $chef->save();

        $manager = Manager::where('branch_id', $chef->branch_id);
        $manager->notify(new NewEmployee());
        return response()->json(['key' => 'chef', 'message' => 'Account verified successfully'], 200);
    }

    // Login user
    public function login(login $request)
    {
        $request->validated();

        $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $chef = Chef::where($loginField, $request->input('login'))->first();
        if (!$chef || !Hash::check($request->input('password'), $chef->password)) {
            return response()->json(['message' => 'User not found or incorrect password'], 404);
        }

        if ($chef->verified_at === null) {
            return response()->json(['message' => 'Verification code not activated']);
        }

        if ($chef->status === 'pending') {
            return response()->json(['message' => 'Not yet approved'], 403);
        }

        if ($chef->status === 'declined') {
            return response()->json(['message' => 'Request declined'], 403);
        }

        $token = $chef->createToken('chef_token')->plainTextToken;

        return response()->json([
            'key' => 'chef',
            'user' => $chef,
            'token' => $token,
        ], 200);
    }

    // Logout user
    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    // Forgot password (Step 1: Send reset code)
    public function forgotPassword(Request $request)
    {
        $request->validate(['identifier' => 'required|string']);

        $isEmail = filter_var($request->identifier, FILTER_VALIDATE_EMAIL);
        $chef = $isEmail
            ? Chef::where('email', $request->identifier)->first()
            : Chef::where('phone', $request->identifier)->first();

        if (!$chef) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $code = mt_rand(1000, 9999);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $isEmail ? $chef->email : $chef->phone],
            ['token' => $code, 'created_at' => now()]
        );

        if ($isEmail) {
            Mail::raw("Your password reset code is: $code", function ($message) use ($chef) {
                $message->to($chef->email)->subject('Password Reset Code');
            });
            return response()->json(['message' => 'Password reset code sent to your email'], 200);
        } else {
            Notification::send($chef, new ResetPassword($code));
            return response()->json(['message' => 'Password reset code sent to your phone'], 200);
        }
    }

    // Reset password (Step 2: Verify code and update password)
    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'code' => 'required|string',
        ]);

        $isEmail = filter_var($request->identifier, FILTER_VALIDATE_EMAIL);
        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $isEmail ? $request->identifier : $request->identifier)
            ->where('token', $request->code)
            ->first();

        if (!$resetToken) {
            return response()->json(['message' => 'Invalid reset code', 'status' => 0], 404);
        }

        return response()->json(['message' => 'Valid reset code', 'status' => 1], 200);
    }

    // Reset password (Step 3: Change password)
    public function resetPassword(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
            'code' => 'required|string',
        ]);

        $isEmail = filter_var($request->identifier, FILTER_VALIDATE_EMAIL);
        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $isEmail ? $request->identifier : $request->identifier)
            ->where('token', $request->code)
            ->first();

        if (!$resetToken) {
            return response()->json(['message' => 'Invalid or expired reset code'], 404);
        }

        $chef = $isEmail
            ? Chef::where('email', $request->identifier)->first()
            : Chef::where('phone', $request->identifier)->first();

        if (!$chef) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $chef->password = Hash::make($request->password);
        $chef->save();

        DB::table('password_reset_tokens')
            ->where('email', $isEmail ? $request->identifier : $request->identifier)
            ->delete();

        return response()->json(['message' => 'Password reset successfully'], 200);
    }
}
