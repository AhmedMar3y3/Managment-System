<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\product\StoreProductRequest;
use App\Http\Requests\product\UpdateProductRequest;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('branch:id,name')->get(['id', 'name']);
        return response()->json($products, 200);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id)->load('branch');
        return response()->json($product, 200);
    }

    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('products'), $imageName);
            $validatedData['image'] = env('APP_URL') . '/products/' . $imageName;
        }
        Product::create($validatedData);
        return response()->json(['message' => 'تم إنشاء المنتج بنجاح'], 201);
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $validatedData = $request->validated();
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('products'), $imageName);
            $validatedData['image'] = env('APP_URL') . '/products/' . $imageName;
        }
        Product::where('id', $id)->update($validatedData);
        return response()->json(['message' => 'تم تعديل المنتج بنجاح'], 200);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'تم حذف المنتج بنجاح'], 200);
    }
}
