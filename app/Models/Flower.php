<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flower extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'branch_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
