<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Specialization;
use App\Models\Branch;
use App\Models\Flower;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function specializations()
    {
        $specializations = Specialization::get(['id', 'name']);
        return response()->json(['specializations' => $specializations], 200);
    }
    public function branches()
    {
        $branches = Branch::get(['id', 'name']);
        return response()->json(['branches' => $branches], 200);
    }

    public function flowers()
    {
        $flowers = Flower::with('branch')->get(['id', 'type', 'branch_id']);
        return response()->json(['flowers' => $flowers], 200);
    }
}
