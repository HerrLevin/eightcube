<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware('api')->group(function () {
    Route::get('nearby', [VenueController::class, 'index'])->name('venues.index');

    Route::resource('statuses', StatusController::class)->except(['create', 'edit']);
    Route::resource('profile', ProfileController::class)->only(['show']);
});


