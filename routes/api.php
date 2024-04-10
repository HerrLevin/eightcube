<?php

use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::get('nearby', [VenueController::class, 'index'])->name('venues.index');
});


