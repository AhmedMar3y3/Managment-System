<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Chef;

class ChefController extends Controller
{
    public function index()
    {
        $chefs = Chef::where('status', 'مقبول')->with('branch')->get(['id', 'first_name', 'last_name','phone','branch_id']);
        $chefs = $chefs->map(function ($chef) {
            $chef->orders_count = $chef->orders()->count();
            return $chef;
        });
        return response()->json($chefs, 200);
    }

    public function show($id)
    {
        $chef = Chef::find($id)->load('orders');
        if ($chef) {
            return response()->json($chef, 200);
        }
        return response()->json('لا يمكن إجراء ذلك', 404);
    }
    public function delete($id)
    {
        $chef = Chef::find($id);
        if ($chef) {
            $chef->delete();
            return response()->json('تم حذف الشيف بنجاح', 200);
        }
        return response()->json('لا يمكن إجراء ذلك', 404);
    }
}
