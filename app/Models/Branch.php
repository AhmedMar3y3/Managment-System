<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'long',
        'lat',
    ];

    public function manager()
    {
        return $this->hasOne(Manager::class);
    }

    public function chefs()
    {
        return $this->hasMany(Chef::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}
