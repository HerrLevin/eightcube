<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class RequestLocation extends Model
{
    use HasFactory;

    protected $fillable = ['latitude', 'longitude', 'last_requested_at'];
}
