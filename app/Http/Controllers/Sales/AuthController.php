<?php

namespace App\Http\Controllers\Sales;


use App\Http\Controllers\Controller;
use App\Http\Requests\sales\login;
use App\Http\Requests\sales\register;
use Illuminate\Http\Request;
use App\Models\Sale;
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
        $sale = Sale::create($validatedData);

        $verificationCode = mt_rand(1000, 9999);
        $sale->verification_code = $verificationCode;
        $sale->save();

        if ($request->has('email')) {
            Mail::raw("Your Verification Code is: $verificationCode", function ($message) use ($sale) {
                $message->to($sale->email)->subject('Verify Your Email Address');
            });

            return response()->json(['message' => 'Verification code has been sent to your email'], 201);
        } elseif ($request->has('phone')) {
            Notification::send($sale, new VerifyPhone($verificationCode));
            return response()->json(['message' => 'Verification code has been sent to your phone'], 201);
        }

        return response()->json(['key' => 'sales', 'message' => 'You have registered your account successfully'], 201);
    }

    // Verify account (email or phone)
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $sale = Sale::where('verification_code', $request->code)->first();

        if (!$sale) {
            return response()->json(['message' => 'The verification code is wrong'], 404);
        }

        $sale->verified_at = now();
        $sale->verification_code = null;
        $sale->save();

        return response()->json(['message' => 'Account verified successfully'], 200);
    }

    // Login user
    public function login(login $request)
    {
        $validatedData = $request->validated();

        $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $sales = Sale::where($loginField, $request->input('login'))->first();
        if (!$sales || !Hash::check($request->input('password'), $sales->password)) {
            return response()->json(['message' => 'User not found or incorrect password'], 404);
        }

        if ($sales->verified_at === null) {
            return response()->json(['message' => 'Verification code not activated']);
        }

        if ($sales->status === 'pending') {
            return response()->json(['message' => 'Not yet approved'], 403);
        }

        if ($sales->status === 'declined') {
            return response()->json(['message' => 'Request has been rejected'], 403);
        }

        $token = $sales->createToken('sales_token')->plainTextToken;

        return response()->json([
            'key' => 'sales',
            'user' => $sales,
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
        $sale = $isEmail
            ? Sale::where('email', $request->identifier)->first()
            : Sale::where('phone', $request->identifier)->first();

        if (!$sale) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $code = mt_rand(1000, 9999);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $isEmail ? $sale->email : $sale->phone],
            ['token' => $code, 'created_at' => now()]
        );

        if ($isEmail) {
            Mail::raw("Your password reset code is: $code", function ($message) use ($sale) {
                $message->to($sale->email)->subject('Password Reset Code');
            });
            return response()->json(['message' => 'Reset code has been sent to your email'], 200);
        } else {
            Notification::send($sale, new ResetPassword($code));
            return response()->json(['message' => 'Reset code has been sent to your phone'], 200);
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
        ]);

        $isEmail = filter_var($request->identifier, FILTER_VALIDATE_EMAIL);
        $sale = $isEmail
            ? Sale::where('email', $request->identifier)->first()
            : Sale::where('phone', $request->identifier)->first();

        if (!$sale) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $sale->password = Hash::make($request->password);
        $sale->save();

        DB::table('password_reset_tokens')
            ->where('email', $isEmail ? $request->identifier : $request->identifier)
            ->delete();

        return response()->json(['message' => 'Password has been reset successfully'], 200);
    }
}
