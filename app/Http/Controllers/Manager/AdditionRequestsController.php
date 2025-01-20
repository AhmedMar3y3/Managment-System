<?php
//___________________________________________________________________________________________________

namespace App\Http\Controllers\Manager;
//___________________________________________________________________________________________________

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Manager;
use App\Models\Chef;
use App\Models\Delivery;
//___________________________________________________________________________________________________

class AdditionRequestsController extends Controller
{
//___________________________________________________________________________________________________
public function Addition()
{
    $manager = auth('manager')->user();
    $manager_branch = $manager->branch_id;
    $chef = Chef::where('status', 'قيد الانتظار')
        ->where('branch_id', $manager_branch)
        ->get(['created_at','phone','first_name','image','id','email']);
    $delivery = Delivery::where('status', 'قيد الانتظار')
        ->where('branch_id', $manager_branch)
        ->get(['created_at','phone','first_name','image','id','email']);
    return response()->json([
        'chef' => $chef,
        'delivery' => $delivery,
    ], 200);
}
//___________________________________________________________________________________________________
public function acceptChef(Request $request)
{
    $validatedData = $request->validate([
        'chef_id' => 'required|integer|exists:chefs,id',
    ]);
    $manager = auth('manager')->user();
    $chef = Chef::where('id', $validatedData['chef_id'])
        ->where('branch_id', $manager->branch_id)
        ->first();
    if (!$chef) {
        return response()->json([
            'message' => 'الشيف غير موجود أو لا ينتمي إلى الفرع الخاص بك',
        ], 404);
    }
    $chef->update(['status' => 'مقبول']);
    return response()->json([
        'message' => 'تم قبول الشيف بنجاح',
    ], 200);
}
//_________________________________________________________________________________________________________

public function rejectChef(Request $request)
{
    $validatedData = $request->validate([
        'chef_id' => 'required|integer|exists:chefs,id',
    ]);
    $manager = auth('manager')->user();
    $chef = Chef::where('id', $validatedData['chef_id'])
        ->where('branch_id', $manager->branch_id)
        ->first();
    if (!$chef) {
        return response()->json([
            'message' => 'الشيف غير موجود أو لا ينتمي إلى الفرع الخاص بك',
        ], 404);
    }
    $chef->update(['status' => 'مرفوض']);
    return response()->json([
        'message' => 'تم رفض الشيف بنجاح',
    ], 200);
} 
//___________________________________________________________________________________________________

public function acceptDelivery(request $request){
    $validatedData=$request->validate([
        'delivery_id'=>'required|integer|exists:deliveries,id',
    ]);
    $manager=auth('manager')->user();
    $delivery=Delivery::where('id',$validatedData['delivery_id'])
    ->where('branch_id',$manager->branch_id)
    ->first();
    if(!$delivery){
        return response()->json(['message'=>'المندوب غير موجود او لا ينتمي الي الفرع']);
    }
    $delivery->update(['status'=>'مقبول']);
    return response()->json([
        'message'=>'تم قبول المندوب',
    ], 200);
}
//___________________________________________________________________________________________________

public function  rejectDelivery(request $request){
    $validatedData=$request->validate([
        'delivery_id'=>'required|integer|exists:deliveries,id',
    ]);
    $manager=auth('manager')->user();
    $delivery=Delivery::where('id',$validatedData['delivery_id'])
    ->where('branch_id',$manager->branch_id)
    ->first();
    if(!$delivery){
        return response()->json(['message'=>'المندوب غير موجود او لا ينتمي الي الفرع']);
    }
    $delivery->update(['status'=>'مرفوض']);
    return response()->json([
        'message'=>'تم رفض المندوب بنجاح',
    ], 200);
}
//___________________________________________________________________________________________________

}
