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
            Mail::raw("رمز التحقق الخاص بك هو: $verificationCode", function ($message) use ($manager) {
                $message->to($manager->email)->subject('رمز التحقق');
            });
            return response()->json(['message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني'], 201);
        } elseif ($request->has('phone')) {
            Notification::send($manager, new VerifyPhone($verificationCode));
            return response()->json(['message' => 'تم إرسال رمز التحقق إلى هاتفك'], 201);
        }
        return response()->json(['message' => 'تم تسجيل المستخدم بنجاح'], 201);
    } 

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);
    
        $manager = Manager::where('verification_code', $request->code)->first();
    
        if (!$manager) {
            return response()->json(['message' => ' الرمز غير صحيح'], 404);
        }
    
        if ($manager->verified_at) {
            return response()->json(['message' => '  تم التحقق بنجاح'], 400);
        }
    
        if ($manager->verification_code_expires_at && $manager->verification_code_expires_at < now()) {
            return response()->json(['message' => 'الرمز لم يعد مفعل'], 400);
        }
    
        $manager->verified_at = now();
        $manager->verification_code = null;
        $manager->save();
    
        return response()->json(['message' => 'تم بنجاح'], 200);
    }


    public function login(login $request)
    {
        $validatedData = $request->validated();
        $manager = Manager::where('email', $request->input('email'))->first();

        if (!$manager || !Hash::check($request->input('password'), $manager->password)) {
            return response()->json(['message' => 'لا يوجد مستخدم أو كلمة المرور غير صحيحة'], 404);
        }

if($manager->verified_at === null){
    return response()->json(['message'=>'لم يتم تفعيل الكود']);

}


        if ($manager->status === 'قيد الانتظار') {
            return response()->json(['message' => 'لم يتم القبول بعد'], 403);
        }

        if ($manager->status === 'مرفوض') {
            return response()->json(['message' => 'لقد تم رفض الطلب'], 403);
        }

        $token = $manager->createToken('manager_token')->plainTextToken;
        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'manager' => $manager,
            'token' => $token,
        ], 200);
        
    }
    
    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح'], 200);
    }

    public function forgotPassword(ForgotRequest $request)
    {
        $validatedData = $request->validated();
        $manager = Manager::where('email', $request->email)->first();
        if (!$manager) {
            return response()->json(['message' => 'لم يتم التعريف']);
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
            'message' => 'ارسل الكود',
        ]);
    }

public function verifyCode(request $request){
    $validatedData = $request->validate([
        'identifier'=>'required|string',
        'code'=>'required|string',
    ]);
    $isEmail = filter_var($request->identifier, FILTER_VALIDATE_EMAIL);
    $token = DB::table('password_reset_tokens')
    ->where('email', $isEmail ? $request->identifier : $request->identifier)
    ->where('token', $request->code)
    ->first();
    
    if (!$token) {
        return response()->json(['message' => ' رمز غير موجود', 'status'=>0], 404);
    }
    return response()->json(['message' => 'الرمز صحيح','status'=>1], 200);
}

public function resetPassword(request $request)
{
    $validatedData = $request->validate([
        'identifier'=>'required|string',
        'code'=>'required|string',
        'password'=>'required|string',
    ]);
    $isEmail = filter_var($request->identifier, FILTER_VALIDATE_EMAIL);
$manager=$isEmail
?Manager::where('email',$request->identifier)->first()
:Manager::where('phone',$request->identifier)->first();

if(!$manager){
    return response()->json(['message'=>'المستخدم غير معرف']);
}

$manager->password = Hash::make($request->password);
$manager->save();

DB::table('password_reset_tokens')
->where('email', $isEmail ? $request->identifier : $request->identifier)
->delete();

return response()->json(['message','تم تعين كلمه مرور جديده بنجاح']);
}


}
