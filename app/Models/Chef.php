<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Chef extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'image', 
        'status',
        'verification_code',
        'verified_at',
        'branch_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ]; 

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function manager(){
    return $this->belongsTo(Manager::class);

    }


    public function report(){
        return $this->hasMany(Report::class);
    }

}
