<?php

namespace App\Backend;

use App\Helpers\LocationHelper;
use App\Models\Venue;
use App\Models\RequestLocation;
use App\Services\OverpassService;
use Illuminate\Support\Collection;

class VenueController
{
    private float $latitude;
    private float $longitude;

    public function __construct(float $latitude, float $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getNearbyVenues()
    {
        // find nearby request locations in database
        $locations = LocationHelper::getNearby(
            RequestLocation::class,
            $this->latitude,
            $this->longitude,
            50,
            [['last_requested_at', '>=', now()->subMinutes(30)]]
        );


        // to reduce the number of requests to the Overpass API, we only request new locations every 30 minutes
        if ($locations->isEmpty()) {
            RequestLocation::create([
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'last_requested_at' => now(),
            ]);

            return $this->venuesFromOverpass();
        }

        return $this->venuesFromDatabase();
    }

    private function venuesFromDatabase()
    {
        return LocationHelper::getNearby(
            Venue::with('tags'),
            $this->latitude,
            $this->longitude,
            200,
            [['name', '!=', '']]
        );
    }

    private function venuesFromOverpass(): Collection
    {
        $nodes = new OverpassService($this->latitude, $this->longitude);
        $nodes = $nodes->getVenues();
        $collection = collect();

        foreach ($nodes as $node) {
            $venue = Venue::updateOrCreate(
                ['osm_type' => $node['type'], 'osm_id' => $node['id']],
                [
                    'name' => $node['name'],
                    'latitude' => $node['latitude'],
                    'longitude' => $node['longitude'],
                ]
            );

            foreach ($venue->tags as $tag) {
                if (!array_key_exists($tag->key, $node['tags'])) {
                    $tag->delete();
                }
            }

            foreach ($node['tags'] as $key => $value) {
                $venue->tags()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            if (strlen($venue->name) > 0) {
                $venue['distance'] = LocationHelper::distance(
                    $this->latitude,
                    $this->longitude,
                    $node['latitude'],
                    $node['longitude']
                );
                $collection->push($venue);
            }
        }

        return $collection;
    }
}
