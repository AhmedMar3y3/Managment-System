<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class HomeController extends Controller
{

    //TODO: remaining in the chef submit a problem and notification
    public function homeStats(){
        $newOrders = Order::where('chef_id', Auth('chef')->id())->where('status', 'تم القبول')->count();
        $inProgressOrders = Order::where('chef_id', Auth('chef')->id())->where('status', 'قيد التجهيز')->count();
        $completedOrders = Order::where('chef_id', Auth('chef')->id())->where('status', 'تم التجهيز')->count();
        return response()->json([ 'طلب جديد' => $newOrders, 'طلب قيد التنفيذ' => $inProgressOrders, 'طلب مكتمل' => $completedOrders], 200);
    }

public static  function indexAll(){
return[
    ['name'=>"حلويات غربيه",
    ['id'=>1,     'name'=> 'ريد فيلفيت',     'price' =>'50',  'image' => asset('orders/1.jpg')],
    ['id'=>2,     'name'=>'مولتن كيك ',      'price' =>'40',  'image' => asset('orders/1.jpg')],
    ['id'=>3,     'name'=>'كرواسون',         'price' =>'30',  'image' => asset('orders/1.jpg')],
    ['id'=>4,     'name'=>'تياك',            'price' =>'100', 'image' => asset('orders/1.jpg')],
],
//_________________________________________________________________________________________________________
    ['name'=>"حلويات شرقيه",
    ['id'=>1,     'name'=> 'ريد فيلفيت',     'price' =>'50',  'image' => asset('orders/1.jpg')],
    ['id'=>2,     'name'=>'مولتن كيك ',      'price' =>'40',  'image' => asset('orders/1.jpg')],
    ['id'=>3,     'name'=>'كرواسون',         'price' =>'30',  'image' => asset('orders/1.jpg')],
    ['id'=>4,     'name'=>'تياك',            'price' =>'100', 'image' => asset('orders/1.jpg')],
],
];
return response()->json(['message'=>self::index()]);
}

}
