<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestLocation extends Model
{
    use HasFactory;

    protected $fillable = ['latitude', 'longitude', 'last_requested_at'];
}
