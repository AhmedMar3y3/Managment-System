<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Manager;
use App\Http\Controllers\Controller;
use App\Notifications\ManagerApproved;

class ManagerController extends Controller
{
    public function pendingManagers()
    {
        $managers = Manager::where('status', 'pending')->with('branch:id,name')->get(['id', 'first_name', 'last_name', 'image', 'branch_id']);
        return response()->json(['key' => 'Manager', 'data' => $managers], 200);
    }

    public function acceptManager($id)
    {
        $manager =  Manager::find($id);
        if ($manager->status == 'pending') {
            $manager->update(['status' => 'approved']);
            $manager->notify(new ManagerApproved($manager));
            return response()->json('Manager accepted', 200);
        }
        return response()->json('Action not allowed', 200);
    }
    public function rejectManager($id)
    {
        $manager = Manager::find($id);
        if ($manager->status == 'pending') {
            $manager->update(['status' => 'declined']);
            return response()->json('Manager rejected', 200);
        }
        return response()->json('Action not allowed', 403);
    }

    public function index()
    {
        $managers = Manager::where('status', 'approved')->with('branch:id,name')->get(['id', 'first_name', 'last_name', 'branch_id']);
        return response()->json($managers, 200);
    }

    public function show($id)
    {
        $manager = Manager::find($id)->load('branch');
        if ($manager) {
            return response()->json($manager, 200);
        }
        return response()->json('Action not allowed', 404);
    }
    public function deleteManager($id)
    {
        $manager = Manager::find($id);
        if ($manager) {
            $manager->delete();
            return response()->json('Manager deleted successfully', 200);
        }
        return response()->json('Action not allowed', 404);
    }
}
