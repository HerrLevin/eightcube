<?php

namespace App\Backend;

use App\Helpers\LocationHelper;
use App\Models\Node;
use App\Models\NodeTag;
use App\Models\RequestLocation;
use App\Services\OverpassService;

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
        // find nearby locations in database
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

            return $this->nodesFromOverpass();
        }

        return $this->nodesFromDatabase();
    }

    private function nodesFromDatabase()
    {
        return LocationHelper::getNearby(Node::with('tags'), $this->latitude, $this->longitude, 200);
    }

    private function nodesFromOverpass(): array
    {
        $venues = new OverpassService($this->latitude, $this->longitude);
        $venues = $venues->getVenues();

        foreach ($venues as $venue) {
            $node = Node::updateOrCreate(
                ['osm_id' => $venue['id']],
                [
                    'name' => $venue['name'],
                    'latitude' => $venue['latitude'],
                    'longitude' => $venue['longitude'],
                ]
            );

            foreach ($node->tags as $tag) {
                if (!array_key_exists($tag->key, $venue['tags'])) {
                    $tag->delete();
                }
            }

            foreach ($venue['tags'] as $key => $value) {
                NodeTag::updateOrCreate(
                    ['node_id' => $node->id, 'key' => $key],
                    ['value' => $value]
                );
            }
        }

        return $venues;
    }
}