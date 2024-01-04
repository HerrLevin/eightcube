<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * œproperty integer $id
 * @property integer $osm_id
 * @property string $osm_type
 * @property string $name
 * @property string $venue_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'osm_id',
        'osm_type',
        'name',
        'venue_type'
    ];

}
