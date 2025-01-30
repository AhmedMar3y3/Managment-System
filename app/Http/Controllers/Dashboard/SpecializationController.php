<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\specialization\store;
use App\Models\Specialization;
use Illuminate\Http\Request;

class SpecializationController extends Controller
{
    public function index()
    {
        $specializations = Specialization::get(['id', 'name']);
        return response()->json($specializations, 200);
    }

    public function store(store $request)
    {
        Specialization::create($request->validated());
        return response()->json(['message' => 'تم إنشاء التخصص بنجاح'], 201);
    }

    public function destroy($id)
    {
        $specializations = Specialization::findOrFail($id);
        $specializations->delete();
        return response()->json(['message' => 'تم حذف التخصص بنجاح'], 200);
    }
}
