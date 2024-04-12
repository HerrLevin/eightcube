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
            ->having('distance', '<', $radius);
        if (!empty($where)) {
            $result->where($where);
        }
        return $result->orderBy('distance')->get();
    }

    public static function distance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo): float
    {
        $theta = $longitudeFrom - $longitudeTo;
        $distance = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo))
            + cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515;
        return round($distance * 1609.344, 2);
    }
}
