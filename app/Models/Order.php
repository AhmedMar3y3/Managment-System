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
        'quantity',
        'flower_id',
        'flower_quantity',
        'image',
        'delivery_date',
        'delivery_time',
        'price',
        'flower_price',
        'delivery_price',
        'total_price',
        'deposit',
        'remaining',
        'customer_name',
        'customer_phone',
        'longitude',
        'latitude',
        'map_desc',
        'additional_data',
        'is_returned',
        'problem',
        'status',
        'product_id',
        'sale_id',
        'manager_id',
        'chef_id',
        'delivery_id',
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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function flowers()
    {
        return $this->belongsTo(Flower::class, 'flower_id');
    }
}
