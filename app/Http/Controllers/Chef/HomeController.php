<?php

namespace App\Http\Controllers\Chef;

use App\Models\Order;
use App\Models\Banner;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    public function banners()
    {
        $banners = Banner::get(['id', 'image']);
        return response()->json($banners, 200);
    }

    public function newOrders()
    {

        $orders = Order::where('chef_id', Auth('chef')->id())->where('status', 'chef waiting')->get(['id', 'order_type', 'order_details', 'delivery_date']);
        return response()->json(['orders' => $orders], 200);
    }

    public function getNotifications()
    {
        $chef = auth('chef')->user();

        $notifications = $chef->notifications()->orderBy('created_at', 'desc')->get();

        if ($notifications->isEmpty()) {
            return response()->json(['message' => 'No notifications available']);
        }

        $formattedNotifications = $notifications->map(function ($notification) {
            return [
                'title'                   => $notification->data['title'] ?? null,
                'id'                      => $notification->data['id'] ?? null,
                'order_type'              => $notification->data['order_type'] ?? null,
                'status'              => $notification->data['status'] ?? null,
                'order_images'            => $notification->data['order_images'] ?? [],
                'notification_created_at' => $notification->created_at,
            ];
        });

        return response()->json(['notifications' => $formattedNotifications]);
    }
}
