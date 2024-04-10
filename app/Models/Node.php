<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'osm_id', 'latitude', 'longitude'];

    public function tags()
    {
        return $this->hasMany(NodeTag::class);
    }
}
