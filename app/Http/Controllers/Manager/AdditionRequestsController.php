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
public function acceptChef(string $id)
{
    $chef = Chef::findOrFail($id);
    if ($chef->branch_id === Auth('manager')->user()->branch_id) {
        $chef->update(['status' => 'مقبول']);
        return response()->json([
            'message' => 'تم قبول الشيف بنجاح',
        ], 200);
    }
    return response()->json([
        'message' => 'الشيف غير موجود أو لا ينتمي إلى الفرع الخاص بك',
    ], 404);
}
//_________________________________________________________________________________________________________

public function rejectChef(string $id)
{

    $chef = Chef::findOrFail($id);

    if ($chef->branch_id === auth('manager')->user()->branch_id) {
        $chef->update(['status' => 'مرفوض']);
    
        return response()->json([
            'message' => 'تم رفض الشيف بنجاح',
        ], 200);
    
    }
    return response()->json([
        'message' => 'الشيف غير موجود أو لا ينتمي إلى الفرع الخاص بك',
    ], 404);

}
//___________________________________________________________________________________________________

public function acceptDelivery(string $id){

    $delivery=Delivery::findOrFail($id);
    if ($delivery->branch_id === auth('manager')->user()->branch_id) {
        
        $delivery->update(['status'=>'مقبول']);
        return response()->json([
            'message'=>'تم قبول المندوب',
        ], 200);
    }
    return response()->json([
        'message' => 'المندوب لا ينتمي الي الفرع الخاص بك او غيلر موجود  ',
    ], 404);
}
//___________________________________________________________________________________________________

public function  rejectDelivery(string $id){

    $delivery=Delivery::findOrFail($id);
    if ($delivery->branch_id === auth('manager')->user()->branch_id) {
    
        $delivery->update(['status'=>'مرفوض']);
        return response()->json([
            'message'=>'تم رفض المندوب بنجاح',
        ], 200);
    }
    return response()->json([
        'message' => 'المندوب لا ينتمي الي الفرع الخاص بك او غيلر موجود  ',
    ], 404);
}
//___________________________________________________________________________________________________

}
