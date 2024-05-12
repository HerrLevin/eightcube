<?php

use App\Http\Controllers\FollowController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware('api')->group(function () {
    Route::get('nearby', [VenueController::class, 'index'])->name('venues.index');

    Route::resource('statuses', StatusController::class)->except(['create', 'edit']);
    Route::resource('profile', ProfileController::class)->only(['show']);

    Route::delete('/settings/external-services/{provider}', [ProfileController::class, 'destroyOAuthProvider'])
        ->name('settings.external-services.destroy');

    Route::post('follow/{user}', [FollowController::class, 'store'])->name('follows.store');
    Route::delete('follow/{follow}', [FollowController::class, 'delete'])->name('follows.destroy');
    Route::get('follows', [FollowController::class, 'index'])->name('follows.index');
});


