<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;

class NotificationsController extends Controller
{
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
