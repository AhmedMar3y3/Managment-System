<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Helpers\DistanceHelper;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    public function newOrders(Request $request)
    {
        $validatedData = $request->validate([
            'lat'  => ['required', 'numeric', 'between:-90,90'],
            'long' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $deliveryLat = $validatedData['lat'];
        $deliveryLon = $validatedData['long'];

        $orders = Order::whereIn('status', ['delivery waiting'])
            ->where('delivery_id', Auth('delivery')->id())
            ->with('images')
            ->get(['id', 'latitude', 'longitude', 'total_price']);

        $orders->each(function ($order) use ($deliveryLat, $deliveryLon) {
            $order->distance = DistanceHelper::haversineDistance(
                $deliveryLat,
                $deliveryLon,
                $order->latitude,
                $order->longitude
            );
        });

        $orders = $orders->sortBy('distance');

        return response()->json([
            'message' => 'Orders fetched successfully',
            'orders'  => $orders,
        ]);
    }

    public function returnedOrders()
    {
        $orders = Order::where('status', 'returned')
            ->where('delivery_id', Auth('delivery')->id())
            ->with('images')
            ->get(['id', 'total_price', 'order_type', 'updated_at']);

        return response()->json([
            'message' => 'Orders fetched successfully',
            'orders'  => $orders,
        ]);
    }

    public function show($id)
    {
        $order = Order::find($id)->load( 'delivery.branch:id,name,lat,long');
        if ($order && $order->delivery_id == Auth('delivery')->id()) {
            return response()->json(new OrderResource($order), 200);
        }
        return response()->json(['message' => 'Unauthorized'], 404);
    }

    public function pendingOrders()
    {
        $orders = Order::where('delivery_id', Auth('delivery')->id())
            ->where('status', 'delivery recieved')
            ->get(['id','updated_at']);
        return response()->json([
            'message' => 'Orders fetched successfully',
            'orders'  => $orders,
        ], 200);
    }
    public function completedOrders()
    {
        $orders = Order::where('delivery_id', Auth('delivery')->id())
            ->where('status', 'delivered')
            ->get(['id','updated_at']);
        return response()->json([
            'message' => 'Orders fetched successfully',
            'orders'  => $orders,
        ], 200);
    }
}
