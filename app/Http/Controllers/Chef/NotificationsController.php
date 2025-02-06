<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;

class NotificationsController extends Controller
{
    public function getNotifications()
    {
        $chef = auth('chef')->user();

        $notifications = $chef->notifications()->orderBy('created_at', 'desc')->get(['data','message']);

        if ($notifications->isEmpty()) {
            return response()->json(['notifications' => 'لا يوجد اشعارات']);
        }

        return response()->json(['notifications' => $notifications]);
    }
}
