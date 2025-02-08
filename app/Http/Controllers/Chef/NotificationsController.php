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
            return response()->json(['notifications' => 'لا يوجد اشعارات']);
        }

        $formattedNotifications = $notifications->map(function ($notification) {
            return [
                'data' => $notification->data,
                'created_at' => $notification->created_at,
            ];
        });

        return response()->json(['notifications' => $formattedNotifications]);
    }
}
