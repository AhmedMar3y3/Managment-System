<?php

namespace App\Http\Controllers\Sales;

use App\Models\Sale;
use App\Http\Requests\sales\update;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\chef\ChangePasswordRequest;

class ProfileController extends Controller
{
    public function getProfile()
    {
        $sale = Auth('sale')->user();
        return response()->json([
            'id' => $sale->id,
            'first_name' => $sale->first_name,
            'last_name' => $sale->last_name,
            'image' => $sale->image,
            'email' => $sale->email,
            'phone' => $sale->phone,
        ], 200);
    }
    public function updateProfile(update $request)
    {
        $user = Auth('sale')->user();
        $sale = Sale::find($user->id);
        $validatedData = $request->validated();
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('users'), $imageName);
            $validatedData['image'] = env('APP_URL') . '/public/users/' . $imageName;
        }
        $sale->update($validatedData);
        return response()->json([
            'first_name' => $sale->first_name,
            'last_name'  => $sale->last_name,
            'email'      => $sale->email,
            'phone'      => $sale->phone,
            'image'      => $sale->image,
            'id'         => $sale->id,
        ], 200);
    }

    public function deleteAccount()
    {
        $user = Auth('sale')->user();
        $sale = Sale::find($user->id);
        $sale->delete();
        return response()->json(['message' => 'تم حذف الحساب بنجاح'], 200);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth('sale')->user();
        $sales = Sale::find($user->id);
        if (Hash::check($request->old_password, $sales->password)) {
            $sales->update(['password' => Hash::make($request->new_password)]);
            return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح'], 200);
        }
        return response()->json(['message' => 'كلمة المرور القديمة غير صحيحة'], 400);
    }
}
