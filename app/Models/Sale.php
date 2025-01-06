<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Sale extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'image',
        'status',
        'remember_token',
        'password',
        'verification_code',
        'verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
