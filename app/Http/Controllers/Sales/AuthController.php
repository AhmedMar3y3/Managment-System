<?php

namespace App\Http\Controllers\Sales;


use App\Http\Controllers\Controller;
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
      
     // Login user
     public function login(Request $request)
     {
         $credentials = $request->only('email', 'password');
     
         $user = Sale::where('email', $credentials['email'])->first();
     
         if (!$user || !Hash::check($credentials['password'], $user->password)) {
             return response()->json(['message' => 'بيانات غير صالحة'], 401);
         }
     
         if (!$user->verified_at) {
             return response()->json(['message' => 'يرجى التحقق من حسابك.'], 401);
         }
         if ($user->status == "مرفوض") {
             return response()->json(['message' => 'تم رفض حسابك.'], 401);
         }


         if ($user->status == "قيد الانتظار") {
             return response()->json(['message' => 'حسابك قيد الانتظار للموافقة.'], 401);
         }
              $token = $user->createToken('sale-token')->plainTextToken;
     
         return response()->json([
             'user' => $user,
             'token' => $token
         ]);


         
     }
     
 
     // Logout user
     public function logout()
     {
         Auth::logout();
         return response()->json(['message' => 'تم تسجيل الخروج بنجاح'], 200);
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
             return response()->json(['message' => 'رمز إعادة التعيين غير صالح', 'status'=>0], 404);
         }
     
         return response()->json(['message' => 'رمز إعادة التعيين صحيح','status'=>1], 200);
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
