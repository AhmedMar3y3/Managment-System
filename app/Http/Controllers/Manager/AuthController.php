<?php

namespace App\Http\Controllers\Manager;

use App\Http\Requests\manager\ForgotRequest;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\manager\register;
use App\Http\Controllers\Controller;
use App\Http\Requests\manager\login;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Notifications\VerifyPhone;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Manager;

class AuthController extends Controller
{
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

        $manager = Manager::create($validatedData);
        $verificationCode = mt_rand(2000, 9999);
        $manager->verification_code = $verificationCode;
        $manager->save();

        if ($request->has('email')) {
            Mail::raw("Your verification code is: $verificationCode", function ($message) use ($manager) {
                $message->to($manager->email)->subject('Verification Code');
            });
            return response()->json(['message' => 'Verification code sent to your email'], 201);
        } elseif ($request->has('phone')) {
            Notification::send($manager, new VerifyPhone($verificationCode));
            return response()->json(['message' => 'Verification code sent to your phone'], 201);
        }
        return response()->json([
            'key' => 'manager',
            'message' => 'User registered successfully'
        ], 201);
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $manager = Manager::where('verification_code', $request->code)->first();

        if (!$manager) {
            return response()->json(['message' => 'Invalid code'], 404);
        }

        if ($manager->verified_at) {
            return response()->json(['message' => 'Already verified, please wait for approval'], 400);
        }

        if ($manager->verification_code_expires_at && $manager->verification_code_expires_at < now()) {
            return response()->json(['message' => 'Code has expired'], 400);
        }

        $manager->verified_at = now();
        $manager->verification_code = null;
        $manager->save();

        return response()->json(['message' => 'Code verified successfully'], 200);
    }

    public function login(login $request)
    {
        $validatedData = $request->validated();

        $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $manager = Manager::where($loginField, $request->input('login'))->first();
        if (!$manager || !Hash::check($request->input('password'), $manager->password)) {
            return response()->json(['message' => 'Invalid user or password'], 404);
        }

        if ($manager->verified_at === null) {
            return response()->json(['message' => 'Code not activated']);
        }

        if ($manager->status === 'pending') {
            return response()->json(['message' => 'Not yet approved'], 403);
        }

        if ($manager->status === 'declined') {
            return response()->json(['message' => 'Request rejected'], 403);
        }

        $token = $manager->createToken('manager_token')->plainTextToken;

        return response()->json([
            'key' => 'manager',
            'user' => $manager,
            'token' => $token,
        ], 200);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function forgotPassword(ForgotRequest $request)
    {
        $validatedData = $request->validated();
        $manager = Manager::where('email', $request->email)->first();
        if (!$manager) {
            return response()->json(['message' => 'User not identified']);
        }

        $code = mt_rand(2000, 99999);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $code, 'created_at' => now()->addMinutes(10)]
        );

        Mail::raw("Your password reset code is: $code", function ($message) use ($manager) {
            $message->to($manager->email)
                ->subject('Password Reset Code');
        });

        return response()->json([
            'message' => 'Code sent',
        ]);
    }

    public function verifyCode(request $request)
    {
        $validatedData = $request->validate([
            'identifier' => 'required|string',
            'code' => 'required|string',
        ]);
        $isEmail = filter_var($request->identifier, FILTER_VALIDATE_EMAIL);
        $token = DB::table('password_reset_tokens')
            ->where('email', $isEmail ? $request->identifier : $request->identifier)
            ->where('token', $request->code)
            ->first();

        if (!$token) {
            return response()->json(['message' => 'Invalid code', 'status' => 0], 404);
        }
        return response()->json(['message' => 'Code is valid', 'status' => 1], 200);
    }

    public function resetPassword(request $request)
    {
        $validatedData = $request->validate([
            'identifier' => 'required|string',
            'code' => 'required|string',
            'password' => 'required|string',
        ]);
        $isEmail = filter_var($request->identifier, FILTER_VALIDATE_EMAIL);
        $manager = $isEmail
            ? Manager::where('email', $request->identifier)->first()
            : Manager::where('phone', $request->identifier)->first();

        if (!$manager) {
            return response()->json(['message' => 'User not identified']);
        }

        $manager->password = Hash::make($request->password);
        $manager->save();

        DB::table('password_reset_tokens')
            ->where('email', $isEmail ? $request->identifier : $request->identifier)
            ->delete();

        return response()->json(['message' => 'Password reset successfully']);
    }
}
