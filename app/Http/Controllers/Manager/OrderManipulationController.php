<?php

namespace App\Http\Controllers\Manager;

use App\Models\Chef;
use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\Chef\orderRecievedToChef;
use App\Notifications\Delivery\OrderRecievedToDelivery;
use App\Notifications\Sales\ManagerAcceptedOrder;

class OrderManipulationController extends Controller
{

    // Accept order
    public function acceptOrder($id)
    {

        $order = Order::findOrFail($id);
        if ($order->status === "new") {
            $order->status = "manager accepted";
            $order->manager_id = Auth::guard('manager')->user()->id;
            $order->save();
            $sales = $order->sale;
            $sales->notify(new ManagerAcceptedOrder($order));
            return response()->json([
                'message' => 'Order accepted successfully',
                'order_id' => $order->id,
                'manager_id' => $order->manager_id,
                'status' => $order->status,
            ]);
        }
        return response()->json(['message' => 'Invalid order status']);
    }


    // Assign order to chef
    public function assignToChef(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'chef_id' => 'required|integer|exists:chefs,id',
        ]);

        $order = Order::find($validatedData['order_id']);
        if ($order) {
            if ($order->status == "manager accepted") {
                $order->update([
                    'status' => 'chef waiting',
                    'chef_id' => $validatedData['chef_id'],
                ]);
                $chefId = $order->chef_id;
                $chef = Chef::find($chefId);
                $chef->notify(new orderRecievedToChef($order));

                return response()->json(['message' => 'Order successfully assigned to chef']);
            }
            return response()->json(['message' => 'Waiting for chef approval']);
        }
        return response()->json(['message' => 'Order not found']);
    }


    // Assign order to delivery
    public function assignOrderToDelivery(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'delivery_id' => 'required|integer|exists:deliveries,id',
        ]);
        $order = Order::find($validatedData['order_id']);
        $order->update([
            'delivery_id' => $validatedData['delivery_id'],
            'status' => 'delivery waiting',
        ]);

        $delivery = Delivery::find($validatedData['delivery_id']);
        $delivery->notify(new OrderRecievedToDelivery($order));
        return response()->json(['message' => 'Order successfully assigned to delivery'], 200);
    }
}
