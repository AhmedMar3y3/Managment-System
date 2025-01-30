<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Http\Requests\branch\store;
use App\Http\Requests\branch\update;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        return response()->json($branches, 200);
    }

    public function store(store $request)
    {
        $validatedDate = $request->validated();
        $branch = Branch::create($validatedDate);
        return response()->json($branch, 201);
    }

    public function show($id)
    {
        $branch = Branch::find($id);
        //->with('manager', 'chefs', 'deliveries')->first()
        if ($branch) {
            return response()->json($branch, 200);
        } else {
            return response()->json(['message' => 'الفرع غير موجود'], 404);
        }
    }

    public function update(update $request, $id)
    {
        $branch = Branch::find($id);
        if ($branch) {
            $validatedDate = $request->validated();
            $branch->update($validatedDate);
            return response()->json($branch, 200);
        } else {
            return response()->json(['message' => 'الفرع غير موجود'], 404);
        }
    }

    public function destroy($id)
    {
        $branch = Branch::find($id);
        if ($branch) {
            $branch->delete();
            return response()->json(['message' => 'تم حذف الفرع'], 200);
        } else {
            return response()->json(['message' => 'الفرع غير موجود'], 404);
        }
    }
}
