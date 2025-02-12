<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Specialization;
use App\Http\Controllers\Controller;
use App\Http\Requests\specialization\store;

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
        return response()->json(['message' => 'Specialization created successfully'], 201);
        }

        public function destroy($id)
        {
        $specializations = Specialization::findOrFail($id);
        $specializations->delete();
        return response()->json(['message' => 'Specialization deleted successfully'], 200);
    }
}
