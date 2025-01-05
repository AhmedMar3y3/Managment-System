<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Chef extends Authenticatable
{
    use HasFactory;

    protected $fillable = [

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
