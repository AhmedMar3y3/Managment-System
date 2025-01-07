<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\sales\update;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function getProfile(){
        $sale = Sale::where('id', Auth('sale')->id())
        ->get(['first_name', 'last_name', 'email', 'phone', 'image','id']);
        return response()->json($sale,200);
    }

    public function updateProfile(update $request){
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
        return response()->json(['message' => 'تم تحديث البيانات بنجاح'], 200);
    }

    public function deleteAccount(){
        $user = Auth('sale')->user();
        $sale = Sale::find($user->id);
        $sale->delete();
        return response()->json(['message' => 'تم حذف الحساب بنجاح'], 200);
    }
}
