<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\manager\register;
use App\Http\Requests\manager\login;
use App\Http\Requests\manager\ResetPass;
use App\Http\Requests\manager\ForgotRequest;
use App\Models\Manager;
use Hash;
use Mail;
use Auth;
use DB;

class AuthController extends Controller
{
    public function register(register $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);
        $manager = Manager::create($validatedData);
        return response()->json(['message' => 'تم انشاء حساب']);
    }

    public function login(login $request)
    {
        $validatedData = $request->validated();
        $manager = Manager::where('email', $request->input('email'))->first();

        if (!$manager || !Hash::check($request->input('password'), $manager->password)) {
            return response()->json(['message' => 'لا يوجد مستخدم أو كلمة المرور غير صحيحة'], 404);
        }

        if ($manager->status == 'قيد الانتظار') {
            return response()->json(['message' => 'لم يتم القبول بعد'], 403);
        }

        if ($manager->status == 'مرفوض') {
            return response()->json(['message' => 'لقد تم رفض الطلب'], 403);
        }

            $token = $manager->createToken('manager_token')->plainTextToken;
            return response()->json([
                'msg' => 'تم تسجيل الدخول بنجاح',
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
            'message' => 'reset code sent to you',
        ]);
    }

    public function resetPassword(ResetPass $request)
    {
        $validatedData = $request->validated();



        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$reset) {
            return response()->json(['message' => 'غير معروف'], 404);
        }

        $manager = Manager::where('email', $request->email)->first();
        $manager->password = Hash::make($request->password);
        $manager->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'تم اعادة التعين بنجاح',
        ], 200);
    }
}
