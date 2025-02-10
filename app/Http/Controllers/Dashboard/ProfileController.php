<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\admin\UpdateProfile;
use App\Http\Requests\chef\ChangePasswordRequest;

class ProfileController extends Controller
{
    public function profile()
    {
        $admin = auth()->user();
        return response()->json(['admin' => $admin], 200);
    }

    public function updateProfile(UpdateProfile $request)
    {
        $admin = auth()->user();
        $admin->update($request->validated());
        return response()->json(['message' => 'تم تحديث البيانات بنجاح'], 200);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $admin = auth()->user();
        if (!Hash::check($request->old_password, $admin->password)) {
            return response()->json(['message' => 'كلمة المرور القديمة غير صحيحة'], 400);
        }
        $admin->update(['password' => Hash::make($request->new_password)]);
        return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح'], 200);
    }
}
