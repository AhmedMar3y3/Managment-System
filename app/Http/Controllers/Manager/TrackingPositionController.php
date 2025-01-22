<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tracking;

class TrackingPositionController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'device_id' => 'required', 
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        $position = Tracking::create($validatedData);
        return response()->json($position);
    }
    
    public function latest($deviceId)
    {
        $position = Tracking::where('device_id', $deviceId)
            ->latest()
            ->first();
        return response()->json($position);
    }     
    
}
