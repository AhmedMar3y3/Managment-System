<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Manager;
use App\Models\Order;
use App\Models\Chef;
use Illuminate\Http\Request;

//______________________________________________________________________________________________________________________
class ManagerController extends Controller
{
//______________________________________________________________________________________________________________________


public function index()

    {
        $order=Order::all();
    return response()->json(['messega' =>$order]);
    }
    //______________________________________________________________________________________________________________________
    
    public function asignToChef(Request $request)
    {

        $validatedData = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'chef_id' => 'required|integer|exists:chefs,id',
        ]);
    
        $order = Order::where('id', $validatedData['order_id'])->first();
    
        if ($order) {
            if ($order->status === "وافق المدير ") {
                return response()->json(['message' => 'تم ارسال الطلب الي الشيف بنجاح   ']);
            }
        }
        return response()->json(['message' => 'الطلب غير موجود']);
    }
//______________________________________________________________________________________________________________________
    public function store(Request $request)
    {






    }

//______________________________________________________________________________________________________________________
    public function show(string $id)
    {
        //
    }

//______________________________________________________________________________________________________________________
    public function edit(string $id)
    {
        //
    }

//______________________________________________________________________________________________________________________
    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
//______________________________________________________________________________________________________________________

}
