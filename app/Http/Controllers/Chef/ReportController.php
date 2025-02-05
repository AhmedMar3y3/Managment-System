<?php

namespace App\Http\Controllers\Chef;

use App\Models\Report;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'problem' => 'required|string|max:500',
        ]);

        $chef = Auth('chef')->user();
        if (!$chef) {
            return response()->json(['message' => 'غير مصرح']);
        }

        Report::create([
            'problem' => $validatedData['problem'],
            'chef_id' => $chef->id,
        ]);

        return response()->json(['message' => 'لقد تم الحفظ']);
    }

    public function show($id)
    {
        $problem = Report::with(['chef'])->findOrFail($id);
        return response()->json(['message' => $problem]);
    }
}
