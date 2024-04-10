<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class OverpassService
{
    private float $latitude;
    private float $longitude;
    private int $radius;
    private Client $client;
    private const array FILTERS = [
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
        $this->client = new Client();
    }

    private function getQuery(): string
    {
        $query = "[out:json][timeout:25];(";
        foreach (static::FILTERS as $key => $filter) {
            if (is_array($filter)) {
                $filters = implode('|', $filter);
                $query .= "node(around:$this->radius,$this->latitude,$this->longitude)[\"$key\"~\"$filters\"];";
            } else {
                $query .= "node(around:$this->radius,$this->latitude,$this->longitude)[\"$filter\"];";
            }
        }

        $query .= ");out;";
        return $query;
    }

    private function getElements(): array
    {
        $query = $this->getQuery();

        $url = "https://overpass-api.de/api/interpreter?data=" . urlencode($query);

        try {
            $response = $this->client->get($url);
        } catch (GuzzleException $e) {
            return [];
        }
        $response = $response->getBody()->getContents();

        return json_decode($response, true);
    }

    public function getVenues(): array
    {
        $response = $this->getElements();

        $venues = [];

        foreach ($response['elements'] as $element) {
            if ($element['type'] === 'node') {
                $venue = [
                    'id' => $element['id'],
                    'name' => $element['tags']['name'] ?? '',
                    'latitude' => $element['lat'],
                    'longitude' => $element['lon'],
                    'tags' => $element['tags'],
                ];

                $venues[] = $venue;
            }
        }

        return $venues;
    }
}
