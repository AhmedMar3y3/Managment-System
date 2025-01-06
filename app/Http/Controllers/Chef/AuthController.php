<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Http\Requests\sales\Login;
use App\Http\Requests\sales\Register;
use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyPhone;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
    // Register a new user
    public function register(Register $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);
        $sale = Sale::create($validatedData);

        $verificationCode = mt_rand(1000, 9999);
        $sale->verification_code = $verificationCode;
        $sale->save();

        if ($request->has('email')) {
            Mail::raw("رمز التحقق الخاص بك هو: $verificationCode", function ($message) use ($sale) {
                $message->to($sale->email)->subject('رمز التحقق');
            });
            return response()->json(['message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني'], 201);
        } elseif ($request->has('phone')) {
            Notification::send($sale, new VerifyPhone($verificationCode));
            return response()->json(['message' => 'تم إرسال رمز التحقق إلى هاتفك'], 201);
        }

        return response()->json(['message' => 'تم تسجيل المستخدم بنجاح'], 201);
    }

    // Login user
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth('sale')->attempt($credentials)) {
            $user = auth('sale')->user();
            if (!$user->verified_at) {
                return response()->json(['message' => 'يرجى التحقق من حسابك.'], 401);
            }
            if($user->status == "مرفوض"){
                return response()->json(['message' => 'تم رفض حسابك.'], 401);
            }
            if($user->status == "قيد الانتظار"){
                return response()->json(['message' => 'حسابك قيد الانتظار للموافقة.'], 401);
            }
            $token = $user->createToken('sale')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token
            ]);
        }
            return response()->json(['message' => 'بيانات الاعتماد غير صالحة'], 401);
    }

    // Logout user
    public function logout()
    {
        auth('sale')->user()->tokens()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح'], 200);
    }

    // Verify account (email or phone)
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $sale = Sale::where('verification_code', $request->code)->first();

        if (!$sale) {
            return response()->json(['message' => 'رمز التحقق غير صالح'], 404);
        }

        $sale->verified_at = now();
        $sale->verification_code = null;
        $sale->save();

        return response()->json(['message' => 'تم التحقق من الحساب بنجاح'], 200);
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
            return response()->json(['message' => 'المستخدم غير موجود'], 404);
        }

        $code = mt_rand(1000, 9999);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $isEmail ? $sale->email : $sale->phone],
            ['token' => $code, 'created_at' => now()]
        );

        if ($isEmail) {
            Mail::raw("رمز إعادة تعيين كلمة المرور الخاص بك هو: $code", function ($message) use ($sale) {
                $message->to($sale->email)->subject('رمز إعادة تعيين كلمة المرور');
            });
            return response()->json(['message' => 'تم إرسال رمز إعادة التعيين إلى بريدك الإلكتروني'], 200);
        } else {
            Notification::send($sale, new ResetPassword($code));
            return response()->json(['message' => 'تم إرسال رمز إعادة التعيين إلى هاتفك'], 200);
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
            return response()->json(['message' => 'رمز إعادة التعيين غير صالح'], 404);
        }
    
        return response()->json(['message' => 'رمز إعادة التعيين صحيح'], 200);
    }
    // Reset password (Step 3: Change password)
    public function changePassword(Request $request)
{
    $request->validate([
        'identifier' => 'required|string',
        'password' => 'required|string|min:8',
    ]);

    $isEmail = filter_var($request->identifier, FILTER_VALIDATE_EMAIL);
    $sale = $isEmail
        ? Sale::where('email', $request->identifier)->first()
        : Sale::where('phone', $request->identifier)->first();

    if (!$sale) {
        return response()->json(['message' => 'المستخدم غير موجود'], 404);
    }

    $sale->password = Hash::make($request->password);
    $sale->save();

    DB::table('password_reset_tokens')
        ->where('email', $isEmail ? $request->identifier : $request->identifier)
        ->delete();

    return response()->json(['message' => 'تم إعادة تعيين كلمة المرور بنجاح'], 200);
}
}
