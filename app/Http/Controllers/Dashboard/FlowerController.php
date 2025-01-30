<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\flower\StoreFlowerRequest;
use App\Models\Flower;
use Illuminate\Http\Request;

class FlowerController extends Controller
{

    public function index()
    {
        $flowers = Flower::get(['id', 'type', 'branch_id']);
        return response()->json($flowers, 200);
    }
    public function store(StoreFlowerRequest $request)
    {
        Flower::create($request->validated());
        return response()->json(['message' => 'تم إضافة الورد بنجاح'], 200);
    }
    public function destroy($id)
    {
        Flower::find($id)->delete();
        return response()->json(['message' => 'تم حذف الورد بنجاح'], 200);
    }
}
