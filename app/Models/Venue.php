<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static updateOrCreate(array $array, array $array1)
 */
class Venue extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'osm_type', 'osm_id', 'latitude', 'longitude'];

    public function tags()
    {
        return $this->hasMany(VenueTag::class);
    }
}
