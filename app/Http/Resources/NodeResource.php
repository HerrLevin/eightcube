<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'distance' => $this->distance ? round($this->distance, 2) : null,
            'tags' => NodeTagResource::collection($this->tags),
        ];
    }
}
