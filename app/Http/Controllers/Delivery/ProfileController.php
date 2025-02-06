<?php

namespace App\Http\Controllers\Delivery;

use App\Models\Delivery;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\delivery\update;
use App\Http\Requests\chef\ChangePasswordRequest;

class ProfileController extends Controller
{
    public function getProfile()
    {
        $delivery = Delivery::where('id', Auth('delivery')->id())
            ->get(['first_name', 'last_name', 'email', 'phone', 'image', 'id']);
        return response()->json($delivery, 200);
    }

    public function updateProfile(update $request)
    {
        $user = Auth('delivery')->user();
        $delivery = delivery::find($user->id);
        $validatedData = $request->validated();
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('users'), $imageName);
            $validatedData['image'] = env('APP_URL') . '/public/users/' . $imageName;
        }
        $delivery->update($validatedData);
        return response()->json(['message' => 'تم تحديث البيانات بنجاح'], 200);
    }

    public function deleteAccount()
    {
        $user = Auth('delivery')->user();
        $delivery = delivery::find($user->id);
        $delivery->delete();
        return response()->json(['message' => 'تم حذف الحساب بنجاح'], 200);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth('delivery')->user();
        $delivery = Delivery::find($user->id);
        if (Hash::check($request->old_password, $delivery->password)) {
            $delivery->update(['password' => Hash::make($request->new_password)]);
            return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح'], 200);
        }
        return response()->json(['message' => 'كلمة المرور القديمة غير صحيحة'], 400);
    }
}
