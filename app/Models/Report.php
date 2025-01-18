<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable=['problem','chef_id'];



    public function chef(){
        return $this->belongsTo(Chef::class);
    }
}
