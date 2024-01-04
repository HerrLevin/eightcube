<?php

namespace App\Http\Controllers;

class OverpassController
{
    private float $latitude;
    private float $longitude;
    private int $radius;
    private array $filters = [
        'amenity' => [
            'cafe',
            'bank',
            'pub',
            'biergarten',
            'restaurant',
            'fast_food',
            'food_court',
            'pharmacy',
            'doctors',
            'clinic',
            'library',
            'toilets',
            'fountain',
        ],
        'historic',
        'tourism',
        'shop',
        'railway' => [
            'station',
            'tram_stop',
        ],
        'public_transport' => [
            'platform',
            'stop_area',
        ],
        'highway' => [
            'bus_stop',
        ],
    ];

    public function __construct(float $latitude, float $longitude, int $radius = 200)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->radius = $radius;
    }

    private function getQuery(): string
    {
        $query = "[out:json][timeout:25];(";
        foreach ($this->filters as $key => $filter) {
            if (is_array($filter)) {
                $filters = implode('|', $filter);
                $query .= "node(around:$this->radius,$this->latitude,$this->longitude)[\"$key\"~\"$filters\"];";
            } else {
                $query .= "node(around:$this->radius,$this->latitude,$this->longitude)[\"$key\"];";
            }
        }

        $query .= ");out;";
        return $query;
    }

    public function getVenues(): array
    {
        $query = $this->getQuery();

        $url = "https://overpass-api.de/api/interpreter?data=" . urlencode($query);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response, true);

        $venues = [];

        foreach ($response['elements'] as $element) {
            if ($element['type'] === 'node') {
                $venue = [
                    'id' => $element['id'],
                    'name' => $element['tags']['name'] ?? '',
                    'latitude' => $element['lat'],
                    'longitude' => $element['lon'],
                    'amenity' => $element['tags']['amenity'] ?? '',
                ];

                $venues[] = $venue;
            }
        }

        return $venues;
    }
}
