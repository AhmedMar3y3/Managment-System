<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_type',
        'order_details',
        'is_sameday',
        'description',
        'image',
        'delivery_date',
        'from',
        'to',
        'cake_price',
        'flower_price',
        'delivery_price',
        'total_price',
        'deposit',
        'customer_name',
        'customer_phone',
        'longitude',
        'latitude',
        'map_desc',
        'additional_data',
        'branch_id',
        'is_returned',
        'problem',
        'status',
        'is_completed',
        'sale_id',
        'manager_id',
        'chef_id',
        'delivery_id',
        'rejection_cause',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }
    public function chef()
    {
        return $this->belongsTo(Chef::class);
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function Images()
    {
        return $this->hasMany(OrderImage::class);
    }

    protected static function booted()
    {
        static::saving(function (Order $order) {
            $cakePrice     = $order->cake_price ?? 0;
            $flowerPrice   = $order->flower_price ?? 0;
            $deliveryPrice = $order->delivery_price ?? 0;
            
            $order->total_price = $cakePrice + $flowerPrice + $deliveryPrice;
        });

        static::creating(function ($order) {
            if (empty($order->delivery_date)) {
                $order->delivery_date = now()->toDateString();
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
