<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\delivery\register;
use Illuminate\Http\Request;
use App\Models\Delivery;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyPhone;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;


class AuthController extends Controller
{

    public function branches(){
        $branches = Branch::get(['id', 'name']);
        return response()->json(['branches' => $branches], 200);
    }
  // Register a new user
  public function register(register $request)
  {
      $validatedData = $request->validated();
      $validatedData['password'] = Hash::make($validatedData['password']);
      if ($request->hasFile('image')) {
         $image = $request->file('image');
         $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
         $image->move(public_path('users'), $imageName);
         $validatedData['image'] = env('APP_URL') . '/users/' . $imageName;
     }
     $delivery = Delivery::create($validatedData);

      $verificationCode = mt_rand(1000, 9999);
      $delivery->verification_code = $verificationCode;
      $delivery->save();

      if ($request->has('email')) {
          Mail::raw("رمز التحقق الخاص بك هو: $verificationCode", function ($message) use ($delivery) {
              $message->to($delivery->email)->subject('رمز التحقق');
          });
          return response()->json(['message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني'], 201);
      } elseif ($request->has('phone')) {
          Notification::send($delivery, new VerifyPhone($verificationCode));
          return response()->json(['message' => 'تم إرسال رمز التحقق إلى هاتفك'], 201);
      }

      return response()->json(['message' => 'تم تسجيل المستخدم بنجاح'], 201);
  }

   // Verify account (email or phone)
   
   public function verify(Request $request)
   {
       $request->validate(['code' => 'required|string']);

       $delivery = Delivery::where('verification_code', $request->code)->first();

       if (!$delivery) {
           return response()->json(['message' => 'رمز التحقق غير صالح'], 404);
       }

       $delivery->verified_at = now();
       $delivery->verification_code = null;
       $delivery->save();

       return response()->json(['message' => 'تم التحقق من الحساب بنجاح'], 200);
   }
   
  // Login user
  public function login(Request $request)
     {
         $credentials = $request->only('email', 'password');
     
         $user = Delivery::where('email', $credentials['email'])->first();
     
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
              $token = $user->createToken('delivery-token')->plainTextToken;
     
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
      $delivery = $isEmail
          ? Delivery::where('email', $request->identifier)->first()
          : Delivery::where('phone', $request->identifier)->first();

      if (!$delivery) {
          return response()->json(['message' => 'المستخدم غير موجود'], 404);
      }

      $code = mt_rand(1000, 9999);
      DB::table('password_reset_tokens')->updateOrInsert(
          ['email' => $isEmail ? $delivery->email : $delivery->phone],
          ['token' => $code, 'created_at' => now()]
      );

      if ($isEmail) {
          Mail::raw("رمز إعادة تعيين كلمة المرور الخاص بك هو: $code", function ($message) use ($delivery) {
              $message->to($delivery->email)->subject('رمز إعادة تعيين كلمة المرور');
          });
          return response()->json(['message' => 'تم إرسال رمز إعادة التعيين إلى بريدك الإلكتروني'], 200);
      } else {
          Notification::send($delivery, new ResetPassword($code));
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
          'code' => 'required|string',
      ]);
  
      $isEmail = filter_var($request->identifier, FILTER_VALIDATE_EMAIL);
      $resetToken = DB::table('password_reset_tokens')
          ->where('email', $isEmail ? $request->identifier : $request->identifier)
          ->where('token', $request->code)
          ->first();
  
      if (!$resetToken) {
          return response()->json(['message' => 'رمز إعادة التعيين غير صالح أو منتهي الصلاحية'], 404);
      }
  
      $delivery = $isEmail
          ? Delivery::where('email', $request->identifier)->first()
          : Delivery::where('phone', $request->identifier)->first();
  
      if (!$delivery) {
          return response()->json(['message' => 'المستخدم غير موجود'], 404);
      }
  
      $delivery->password = Hash::make($request->password);
      $delivery->save();
  
      DB::table('password_reset_tokens')
          ->where('email', $isEmail ? $request->identifier : $request->identifier)
          ->delete();
  
      return response()->json(['message' => 'تم إعادة تعيين كلمة المرور بنجاح'], 200);
  }
  
}
