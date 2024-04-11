<?php

namespace App\Http\Controllers;

use App\Http\Resources\StatusResource;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StatusController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $statuses = Status::with('user', 'venue')->get();
        return StatusResource::collection($statuses);
    }

    public function store(Request $request): StatusResource
    {
        $request->validate([
            'body' => 'nullable|string|max:255',
            'venue_id' => 'required|exists:venues,id',
        ]);

        $user = $request->user();
        $status = $user->statuses()->create($request->only('body', 'venue_id'));

        return new StatusResource($status);
    }

    public function show(Status $venue)
    {

    }

    public function edit(Status $venue)
    {
        //
    }

    public function update(Request $request, Status $venue)
    {
        //
    }

    public function destroy(Status $venue)
    {
        //
    }
}
