<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;


class Manager extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'image',
        'branch_id',
        'status',
        'verification_code',
        'verified_at',
        'password',
        'remember_token',

    ];

    protected $hidden = [
        'password',
        'remember_token', 
    
        
    ]; 

    public function branch(){
    return $this->belongsTo(Branch::class);
    }

public function order(){
    return $this->hasMany(Order::class);
}

public function chef()
{
    return $this->hasMany(Chef::class);
} 

}
