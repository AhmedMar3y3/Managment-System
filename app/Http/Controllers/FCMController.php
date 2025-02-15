<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FCMController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $user = $request->user();

        $user->update(['fcm_token' => $validated['fcm_token']]);

        return response()->json([
            'message' => 'FCM token stored successfully'
        ]);
    }
}
