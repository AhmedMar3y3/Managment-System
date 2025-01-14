<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Manager;
use App\Models\Order;
use App\Models\Chef;
use App\Models\Branch;
use App\Notifications\SendToManager;
use Auth;
use Illuminate\Http\Request;

//__//__//___________//____________//_//________________//_________________//___//______________//_______________//____________//___//__________//
//_//____//___________//___________//_//_________________//_________________//___//______________//_______________//_____________//___//___________//
class ManagerController extends Controller
{
//__//__//___________//____________//_//________________//_________________//___//______________//_______________//____________//___//__________//
//_//____//___________//___________//_//_________________//_________________//___//______________//_______________//_____________//___//___________//
public function index()
    {
        $order=Order::all();
    return response()->json(['messega' =>$order]);
    }
 //__//__//___________//____________//_//________________//_________________//___//______________//_______________//____________//___//__________//
//_//____//___________//___________//_//_________________//_________________//___//______________//_______________//_____________//___//___________//
    public function asignToChef(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'chef_id' => 'required|integer|exists:chefs,id',
        ]);
        $order = Order::where('id', $validatedData['order_id'])->first();
        if ($order) {
            if ($order->status == "وافق المدير" || $order->status == "تم الرفض") {
                
                Order::create([
                    'chef_id' => $validatedData['chef_id'],
                ]);
                return response()->json(['message' => 'تم ارسال الطلب الي الشيف بنجاح   ']);
            }
            return response()->json(['message' => ' في  انتظار موافقه الشيف']);
        }
        return response()->json(['message' => 'الطلب غير موجود']);
    }
//__//__//___________//____________//_//________________//_________________//___//______________//_______________//____________//___//__________//
//_//____//___________//___________//_//_________________//_________________//___//______________//_______________//_____________//___//___________//
    public function acceptOrder($id)
    {
        $order = Order::findOrFail($id);
        if ($order->status === "جاري الاستلام") {
            if ($order->status === "وافق المدير") {
                $order->manager_id = Auth('manager')->user()->id();
                return response()->json([
                    'message' => 'تم جلب بيانات المدير بنجاح',
                    'manager_id' => $order->manager_id,
                ]);
            }
            return response()->json(['message' => 'لم يتم الموافقة على الطلب']);
        }
        return response()->json(['message' => 'حالة الطلب غير صحيحة']);
    }
//__//__//___________//____________//_//________________//_________________//___//______________//_______________//____________//___//__________//
//_//____//___________//___________//_//_________________//_________________//___//______________//_______________//_____________//___//___________//
    public function show(string $id)
    {
        //
    }
//__//__//___________//____________//_//________________//_________________//___//______________//_______________//____________//___//__________//
//_//____//___________//___________//_//_________________//_________________//___//______________//_______________//_____________//___//___________//
    public function edit(string $id)
    {
        //
    }
//__//__//___________//____________//_//________________//_________________//___//______________//_______________//____________//___//__________//
//_//____//___________//___________//_//_________________//_________________//___//______________//_______________//_____________//___//___________//
    public function update(Request $request, string $id)
    {
        //
    }
    public function destroy(string $id)
    {
        //
    }
//__//__//___________//____________//_//________________//_________________//___//______________//_______________//____________//___//__________//
//_//____//___________//___________//_//_________________//_________________//___//______________//_______________//_____________//___//___________//

}


