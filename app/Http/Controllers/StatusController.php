<?php

namespace App\Http\Controllers;

use App\Http\Resources\StatusResource;
use App\Models\Status;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application as FoundationApplication;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

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

    public function show(int $id): StatusResource
    {
        $status = Status::with('user', 'venue')->findOrFail($id);
        return new StatusResource($status);
    }

    public function update(Request $request, int $id): StatusResource
    {
        $status = Status::findOrFail($id);
        $request->validate([
            'body' => 'nullable|string|max:255',
        ]);

        // Todo: implement with policy
        if ($status->user_id !== $request->user()->id) {
            abort(403);
        }

        $status->update($request->only('venue_id'));

        return new StatusResource($status);
    }

    public function destroy(Request $request, int $id): FoundationApplication|Response|Application|ResponseFactory
    {
        $status = Status::findOrFail($id);

        // Todo: implement with policy
        if ($status->user_id !== $request->user()->id) {
            abort(403);
        }

        $status->delete();

        return response(null, 204);
    }
}
