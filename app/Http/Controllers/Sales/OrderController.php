<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Notifications\SendToManager;
use App\Http\Requests\order\store;
use App\Http\Requests\order\update;
use App\Models\Order;
use App\Models\Manager;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('sale_id', Auth('sale')->id())->get(['order_type', 'status', 'delivery_date', 'customer_name']);
        return response()->json(['orders' => $orders], 200);
        
    }

    public function store(store $request)
    {
        $validatedData = $request->validated();
        $validatedData['sale_id'] = Auth('sale')->id();
        $order = Order::create($validatedData);
        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
            
                    $destinationPath = public_path('orders');
                    $fileName = uniqid() . '_' . $image->getClientOriginalName();
                    $image->move($destinationPath, $fileName);
                    $imageFullUrl = url("orders/{$fileName}");
                    $order->images()->create([
                        'image' => $imageFullUrl,
                        'order_id' => $order->id,
                    ]);
            }
        }

        $managers = Manager::all();
    foreach ($managers as $manager) {
        $manager->notify(new SendToManager($order));
    
    }
        return response()->json(['message' => 'تم إنشاء الطلب بنجاح', 'order' => $order], 201);
    }

    public function show($id)
    {
        $order = Order::where('sale_id', Auth('sale')->id())->findOrFail($id)->load('images');
        return response()->json(['order' => $order], 200);
    }
    public function update(update $request, $id)
    {
        $order = Order::find($id);
        $validatedData = $request->validated();
        $order->update($request->except('images'));

        if ($request->has('images')) {
            $order->images()->delete();

            foreach ($request->file('images') as $image) {
                try {
                    $destinationPath = public_path('orders');
                    $fileName = uniqid() . '_' . $image->getClientOriginalName();
                    $image->move($destinationPath, $fileName);
                    $imageFullUrl = url('orders/' . $fileName);

                    $order->images()->create([
                        'image' => $imageFullUrl,
                        'order_id' => $order->id,
                    ]);
                } catch (\Exception $e) {
                    return redirect()->back()->withErrors(['error' => $e->getMessage()]);
                }
            }
        }

        return response()->json(['message' => 'تم تحديث الطلب بنجاح', 'order' => $order], 200);
    }

    public function newOrders()
    {
        $orders = Order::whereDate('created_at', today())
        ->where('sale_id', Auth('sale')->id())
        ->get(['id','order_type', 'status', 'delivery_date', 'customer_name']);
        return response()->json(['orders' => $orders], 200);
    }

    public function preparingOrders()
    {
        $orders = Order::where('status', 'قيد التنفيذ')
        ->where('sale_id', Auth('sale')->id())
        ->get(['id','order_type', 'status', 'delivery_date', 'customer_name']);
        return response()->json(['orders' => $orders], 200);
    }

    public function deliveredOrders()
    {
        $orders = Order::where('status', 'تم التوصيل')
        ->where('sale_id', Auth('sale')->id())
        ->get(['id','order_type', 'status', 'delivery_date', 'customer_name']);
        return response()->json(['orders' => $orders], 200);
    }

}