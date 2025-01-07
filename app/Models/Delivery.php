<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Delivery extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
