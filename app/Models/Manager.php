<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Manager extends Authenticatable
{
    use HasFactory;

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
}
