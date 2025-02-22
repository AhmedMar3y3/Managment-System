<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        "delivery_id",
        "long",
        "lat"
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
}
