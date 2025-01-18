<?php
//______________________________________________________________________________________________

namespace App\Http\Controllers\Manager;
//______________________________________________________________________________________________

use App\Http\Controllers\Controller;
use App\Notifications\SendToManager;
use Illuminate\Http\Request;
use App\Models\Manager;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Chef;
use Auth;
//______________________________________________________________________________________________

class ManagerController extends Controller
{
// ______________________________________________________________________________________________

// ______________________________________________________________________________________________
public function assignToChef(Request $request)
{
    $validatedData = $request->validate([
        'order_id' => 'required|integer|exists:orders,id',
        'chef_id' => 'required|integer|exists:chefs,id',
    ]);

    $order = Order::find($validatedData['order_id']);
    if ($order) {
        if ($order->status == "وافق المدير" || $order->status == "تم الرفض") {
            $order->update([
                'chef_id' => $validatedData['chef_id'],
                // 'status' => 'وافق المدير',
            ]);

            return response()->json(['message' => 'تم ارسال الطلب إلى الشيف بنجاح']);
        }
        return response()->json(['message' => 'في انتظار موافقة الشيف']);
    }
    return response()->json(['message' => 'الطلب غير موجود']);
}

//______________________________________________________________________________________________
public function acceptOrder($id)
{
    $order = Order::findOrFail($id);
    if ($order->status === "جاري الاستلام") {
        $order->status = "وافق المدير";
        $order->manager_id = Auth::guard('manager')->user()->id;
        $order->save();
        
        return response()->json([
            'message' => 'تم الموافقة على الطلب بنجاح',
            'manager_id' => $order->manager_id,
            'status' => $order->status,
        ]);
    }
    return response()->json(['message' => 'حالة الطلب غير صحيحة']);
}
//______________________________________________________________________________________________
// public function status()
// {
//     $order = Manager::where('status', 'قيد الانتظار')->first();
    
//     if ($order) {
//         $order->status = 'مقبول';
//         $order->save(); 
//     }
//     return response()->json(['message' => 'تم تغيير الحالة إلى مقبول', 'order' => $order]);
// }
//______________________________________________________________________________________________
// public function switchStatusToApproved($id)
// {
//     // Find the manager by ID
//     $manager = Manager::find($id);
    
//     if (!$manager) {
//         return response()->json([
//             'success' => false,
//             'message' => "Manager with ID $id does not exist."
//         ], 404);
//     }
//     $manager->status = 'مقبول';
//     $manager->save();
//     return response()->json([
//         'success' => true,
//         'message' => "Manager with ID $id has been updated to مقبول."
//     ]);
// }
//______________________________________________________________________________________________

}


