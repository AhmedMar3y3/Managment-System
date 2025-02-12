<?php

namespace App\Http\Controllers\Sales;

use App\Models\Order;
use App\Models\Manager;
use App\Notifications\SendToManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\order\update;
use App\Http\Requests\order\storeFirst;
use App\Http\Requests\order\storeSecond;
use App\Http\Requests\order\storeThird;

class OrderController extends Controller
{

    public function search()
    {
        $search = request('search');
        $orders = Order::where(function ($query) use ($search) {
                $query->where('customer_name', 'like', '%' . $search . '%')
                      ->orWhere('id', 'like', '%' . $search . '%')
                      ->orWhere('customer_phone', 'like', '%' . $search . '%');
            })
            ->where('status', '!=', 'delivered')
            ->get(['id', 'order_type', 'status', 'delivery_date', 'customer_name']);
        return response()->json(['orders' => $orders], 200);
    }
    public function index()
    {
        $orders = Order::get(['id', 'order_type', 'status', 'delivery_date', 'customer_name']);
        return response()->json(['orders' => $orders], 200);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id)->load('images',);
        return response()->json(['order' => $order], 200);
    }

    public function storeFirstScreen(storeFirst $request)
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

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = uniqid() . '_' . $image->getClientOriginalName();
            $image->move(public_path('orders'), $fileName);
            $validatedData['image'] = url("orders/{$fileName}");

            $order->update(['image' => $validatedData['image']]);
        }

        $order->load('images');

        return response()->json([
            'message' => 'You have created the order successfully',
            'order'   => $order,
        ], 201);
    }

    // Second Screen: Update Order with Price Details
    public function storeSecondScreen(storeSecond $request, Order $order)
    {
        $validatedData = $request->validated();
        $order->update($validatedData);

        return response()->json([
            'message' => 'Order updated successfully (Second Screen)',
            'updated_data' => $validatedData,
        ], 200);
    }


    // Third Screen: Update Order with Customer and Location Details
    public function storeThirdScreen(storeThird $request, Order $order)
    {
        $validatedData = $request->validated();
        $order->update($validatedData);

        // $managers = Manager::where('status', 'approved')->get();
        // foreach ($managers as $manager) {
        //     $manager->notify(new SendToManager($order));
        // }

        return response()->json([
            'message' => 'Order updated successfully (Third Screen)',
            'updated_data' => $validatedData,
        ], 200);
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

        return response()->json(['message' => 'Order updated successfully', 'order' => $order], 200);
    }

    public function newOrders()
    {
        $orders = Order::whereDate('created_at', today())
            ->where('sale_id', Auth('sale')->id())->with('images')
            ->get(['id','updated_at']);
        return response()->json(['orders' => $orders], 200);
    }

    public function preparingOrders()
    {
        $orders = Order::where('status', 'inprogress')
            ->where('sale_id', Auth('sale')->id())
            ->get(['id','updated_at']);
        return response()->json(['orders' => $orders], 200);
    }

    public function deliveredOrders()
    {
        $orders = Order::where('status', 'delivered')
            ->where('sale_id', Auth('sale')->id())
            ->get(['id','updated_at']);
        return response()->json(['orders' => $orders], 200);
    }
}
