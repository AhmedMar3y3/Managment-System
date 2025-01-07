<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_phone',
        'customer_address',
        'order_type',
        'order_details',
        'status',
        'price',
        'deposit',
        'delivery_date',
        'notes',
        'sale_id',
        'chef_id',
        'delivery_id',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
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
}
