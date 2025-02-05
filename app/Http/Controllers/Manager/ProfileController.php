<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\manager\update;

class ProfileController extends Controller
{
    public function getProfilemanager()
    {
        $manager = auth('manager')->user();
        return response()->json([
            'first_name' => $manager->first_name,
            'last_name'  => $manager->last_name,
            'email'      => $manager->email,
            'phone'      => $manager->phone,
            'image'      => $manager->image,
            'id'         => $manager->id,
        ], 200);
    }
    public function updateProfilemanager(update $request)
    {
        $manager = auth('manager')->user();
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('users'), $imageName);
            $validatedData['image'] = env('APP_URL') . '/users/' . $imageName;
        }

        $manager->update($validatedData);

        return response()->json([
            'first_name' => $manager->first_name,
            'last_name'  => $manager->last_name,
            'email'      => $manager->email,
            'phone'      => $manager->phone,
            'image'      => $manager->image,
            'id'         => $manager->id,
        ], 200);
    }
    public function deleteAccountmanager()
    {
        $manager = auth('manager')->user();
        $manager->delete();
        return response()->json(['message' => 'تم حذف الحساب بنجاح'], 200);
    }
}
