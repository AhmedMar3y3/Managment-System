<?php

namespace App\Http\Controllers\chef;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{public function getNotifications()
    {
        $chef = auth('chef')->user();

        $notifications = $chef->notifications()->orderBy('created_at', 'desc')->get();

        if ($notifications->isEmpty()) {
            return response()->json(['notifications' => 'لا يوجد اشعارات']);
        }

        return response()->json(['notifications' => $notifications]);
    }
    
    
}
