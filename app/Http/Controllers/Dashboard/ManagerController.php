<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Manager;
use App\Notifications\ManagerApproved;

class ManagerController extends Controller
{
    public function pendingManagers()
    {
        $managers = Manager::where('status', 'قيد الانتظار')->with('branch:id,name')->get(['id', 'first_name', 'last_name','image', 'branch_id']);
        return response()->json(['key'=>'مدير','data'=>$managers], 200);
    }

    // make notification for approved manager on email

    public function acceptManager($id)
    {
        $manager =  Manager::find($id);
        if ($manager->status == 'قيد الانتظار') {
            $manager->update(['status' => 'مقبول']);
            $manager->notify(new ManagerApproved($manager));
            return response()->json('تم قبول المدير', 200);
        }
        return response()->json('لا يمكن إجراء ذلك', 200);
    }
    public function rejectManager($id)
    {
        $manager = Manager::find($id);
        if ($manager->status == 'قيد الانتظار') {
            $manager->update(['status' => 'مرفوض']);
            return response()->json('تم رفض المدير', 200);
        }
        return response()->json('لا يمكن إجراء ذلك', 403);
    }

    public function index()
    {
        $managers = Manager::where('status', 'مقبول')->with('branch:id,name')->get(['id', 'first_name', 'last_name', 'branch_id']);
        return response()->json($managers, 200);
    }

    public function show($id)
    {
        $manager = Manager::find($id)->load('branch');
        if ($manager) {
            return response()->json($manager, 200);
        }
        return response()->json('لا يمكن إجراء ذلك', 404);
    }
    public function deleteManager($id)
    {
        $manager = Manager::find($id);
        if ($manager) {
            $manager->delete();
            return response()->json('تم حذف المدير بنجاح', 200);
        }
        return response()->json('لا يمكن إجراء ذلك', 404);
    }
}
