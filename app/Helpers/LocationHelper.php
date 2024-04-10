<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;

class LocationHelper
{
    public static function getNearby(string|Builder $model, $latitude, $longitude, $radius = 10, $where = [])
    {
        if (!$model instanceof Builder) {
            $result = $model::select('*');
        } else {
            $result = $model->select('*');
        }
        $result->selectRaw(
            '(6371000
                    * acos(cos(radians(?))
                    * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?))
                    + sin(radians(?)) * sin(radians(latitude)))
                ) AS distance',
            [$latitude, $longitude, $latitude]
        )
            ->where('distance', '<', $radius);
        if (!empty($where)) {
            $result->where($where);
        }
        return $result->orderBy('distance')->get();
    }
}
