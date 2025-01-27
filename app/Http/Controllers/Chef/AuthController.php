<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Http\Requests\chef\register;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Chef;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyPhone;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;


//Check CI/CD
class AuthController extends Controller
{

    public function branches(){
        $branches = Branch::where('id', Auth('chef')->user()->branch_id)->get(['id', 'name']);
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
         $validatedData['image'] = env('APP_URL') . '/public/users/' . $imageName;
     }
     $chef = Chef::create($validatedData);

      $verificationCode = mt_rand(1000, 9999);
      $chef->verification_code = $verificationCode;
      $chef->save();

      if ($request->has('email')) {
          Mail::raw("رمز التحقق الخاص بك هو: $verificationCode", function ($message) use ($chef) {
              $message->to($chef->email)->subject('رمز التحقق');
          });
          return response()->json(['message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني'], 201);
      } elseif ($request->has('phone')) {
          Notification::send($chef, new VerifyPhone($verificationCode));
          return response()->json(['message' => 'تم إرسال رمز التحقق إلى هاتفك'], 201);
      }

      return response()->json(['message' => 'تم تسجيل المستخدم بنجاح'], 201);
  }

   // Verify account (email or phone)
   
   public function verify(Request $request)
   {
       $request->validate(['code' => 'required|string']);

       $chef = Chef::where('verification_code', $request->code)->first();

       if (!$chef) {
           return response()->json(['message' => 'رمز التحقق غير صالح'], 404);
       }

       $chef->verified_at = now();
       $chef->verification_code = null;
       $chef->save();

       return response()->json(['message' => 'تم التحقق من الحساب بنجاح'], 200);
   }
   
  // Login user
  public function login(Request $request)
     {
         $credentials = $request->only('email', 'password');
     
         $user = Chef::where('email', $credentials['email'])->first();
     
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
              $token = $user->createToken('chef-token')->plainTextToken;
     
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
      $chef = $isEmail
          ? Chef::where('email', $request->identifier)->first()
          : Chef::where('phone', $request->identifier)->first();

      if (!$chef) {
          return response()->json(['message' => 'المستخدم غير موجود'], 404);
      }

      $code = mt_rand(1000, 9999);
      DB::table('password_reset_tokens')->updateOrInsert(
          ['email' => $isEmail ? $chef->email : $chef->phone],
          ['token' => $code, 'created_at' => now()]
      );

      if ($isEmail) {
          Mail::raw("رمز إعادة تعيين كلمة المرور الخاص بك هو: $code", function ($message) use ($chef) {
              $message->to($chef->email)->subject('رمز إعادة تعيين كلمة المرور');
          });
          return response()->json(['message' => 'تم إرسال رمز إعادة التعيين إلى بريدك الإلكتروني'], 200);
      } else {
          Notification::send($chef, new ResetPassword($code));
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
  
      $chef = $isEmail
          ? Chef::where('email', $request->identifier)->first()
          : Chef::where('phone', $request->identifier)->first();
  
      if (!$chef) {
          return response()->json(['message' => 'المستخدم غير موجود'], 404);
      }
  
      $chef->password = Hash::make($request->password);
      $chef->save();
  
      DB::table('password_reset_tokens')
          ->where('email', $isEmail ? $request->identifier : $request->identifier)
          ->delete();
  
      return response()->json(['message' => 'تم إعادة تعيين كلمة المرور بنجاح'], 200);
  }
  
}
