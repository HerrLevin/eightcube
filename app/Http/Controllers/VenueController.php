<?php

namespace App\Http\Controllers;

use App\Helpers\LocationHelper;
use App\Models\Node;
use App\Models\NodeTag;
use App\Models\RequestLocation;
use App\Services\OverpassService;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate latitude and longitude
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['required', 'numeric', 'min:-180', 'max:180'],
        ]);

        $latitude = $validated['latitude'];
        $longitude = $validated['longitude'];

        // find nearby locations in database
        $locations = LocationHelper::getNearby(
            RequestLocation::class,
            $latitude,
            $longitude,
            50,
            [['last_requested_at', '>=', now()->subMinutes(30)]]
        );


        // to reduce the number of requests to the Overpass API, we only request new locations every 30 minutes
        if ($locations->isEmpty()) {
            RequestLocation::create([
                'latitude' => $latitude,
                'longitude' => $longitude,
                'last_requested_at' => now(),
            ]);

            return $this->nodesFromOverpass($latitude, $longitude);
        }

        return $this->nodesFromDatabase($latitude, $longitude);
    }

    private function nodesFromDatabase($latitude, $longitude)
    {
        return LocationHelper::getNearby(Node::with('tags'), $latitude, $longitude, 200);
    }

    private function nodesFromOverpass($latitude, $longitude): array
    {
        $venues = new OverpassService($latitude, $longitude);
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Venue $venue)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venue $venue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venue $venue)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venue $venue)
    {
        //
    }
}
