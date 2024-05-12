<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class FollowController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id'
        ]);
        $userId = $request->user_id ?? auth()->id();
        $follows = Follow::where('follower_id', $userId)->get();

        return UserResource::collection($follows);
    }

    public function store(Request $request, int $user): UserResource
    {
        $user = User::findOrFail($user);

        if (!$user->isFollowing && $user->id !== $request->user()->id) {
            Follow::create([
                'follower_id' => $request->user()->id,
                'followee_id' => $user->id,
            ]);
        }

        return new UserResource($user->refresh());
    }

    public function delete(Request $request, int $user)
    {
        $user = User::findOrFail($user);
        $follow = Follow::where([
            'follower_id' => $request->user()->id,
            'followee_id' => $user->id,
        ])->firstOrFail();

        Gate::authorize('delete', $follow);

        $follow->delete();
        return new UserResource($user->refresh());
    }
}
