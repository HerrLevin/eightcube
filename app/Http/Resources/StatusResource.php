<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'user' => new UserResource($this->user),
            'venue' => new VenueResource($this->venue),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
