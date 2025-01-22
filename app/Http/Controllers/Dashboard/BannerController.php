<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\banner\store;
use Illuminate\Http\Request;
use App\Models\Banner;

class BannerController extends Controller
{
    public function index(){

        $banner = Banner::get(['id','title']);
        return response()->json($banner,200);
    }

    public function store(store $request){
        $validated = $request->validated();
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('banners'), $imageName);
            $validated['image'] = env('APP_URL') . '/public/banners/' . $imageName;
        }
       Banner::create($validated);
       return response()->json('تم اضافة البانر بنجاح',200);
    }
    public function show($id){
        $banner = Banner::find($id)->get(['id','title','image']);
        return response()->json($banner,200);
    }

    public function destroy($id){
        Banner::find($id)->delete();
        return response()->json('تم مسح البانر بنجاح',200);
    }
}
