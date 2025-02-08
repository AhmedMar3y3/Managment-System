<?php

namespace App\Http\Controllers\Chef;

use App\Models\Chef;
use App\Http\Requests\chef\update;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\chef\ChangePasswordRequest;

class ProfileController extends Controller
{
    public function getProfile()
    {
        $chef = Chef::where('id', Auth('chef')->id())
            ->get(['first_name', 'last_name', 'email', 'phone', 'image', 'id']);
        return response()->json([
            'id' => $chef->id,
            'first_name'=> $chef->first_name,
            'last_name'=> $chef->last_name,
            'image'=> $chef->image,
            'email'=> $chef->email,
            'phone'=> $chef->phone,
        ], 200);
    }

    public function updateProfile(update $request)
    {
        $user = Auth('chef')->user();
        $chef = Chef::find($user->id);
        $validatedData = $request->validated();
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('users'), $imageName);
            $validatedData['image'] = env('APP_URL') . '/public/users/' . $imageName;
        }
        $chef->update($validatedData);
        return response()->json(['message' => 'تم تحديث البيانات بنجاح'], 200);
    }

    public function deleteAccount()
    {
        $user = Auth('chef')->user();
        $chef = Chef::find($user->id);
        $chef->delete();
        return response()->json(['message' => 'تم حذف الحساب بنجاح'], 200);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth('chef')->user();
        $chef = Chef::find($user->id);
        if (Hash::check($request->old_password, $chef->password)) {
            $chef->update(['password' => Hash::make($request->new_password)]);
            return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح'], 200);
        }
        return response()->json(['message' => 'كلمة المرور القديمة غير صحيحة'], 400);
    }
}
