<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Chef;

class ReportController extends Controller
{
    
    //____________________________________________________________________________________________
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'problem' => 'required|string|max:500',
        ]);
    
        $chef = auth('chef')->user();
        if (!$chef) {
            return response()->json(['message' => 'غير مصرح']);
        }
    
            Report::create([
            'problem'=>$validatedData['problem'],
            'chef_id'=>$chef->id,
        ]);
    
        return response()->json(['message' => 'لقد تم الحفظ']);
    }
    
    //____________________________________________________________________________________________
    public function show(string $id)
    {
        $problem = Report::with(['chef'])->findOrFail($id);
        return response()->json(['message'=>$problem]);
    }
    //____________________________________________________________________________________________
    
    }
    