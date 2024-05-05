<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'version' => "0.0",
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/checkin', function () {
    return Inertia::render('Checkin');
})->middleware(['auth', 'verified'])->name('checkin');

Route::get('/status/{statusId}', function () {
    return Inertia::render('StatusDetail', [
        'statusId' => request()->route('statusId')
    ]);
})->name('status');

Route::get('/profile/{userId}', function () {
    return Inertia::render('Profile', [
        'userId' => request()->route('userId')
    ]);
})->name('profile');


Route::middleware('auth')->group(function () {
    Route::get('/settings/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/settings/edit', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/settings/edit', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/auth/redirect/{provider}', [SocialiteController::class, 'redirectToProvider']);
Route::get('/auth/callback/{provider}', [SocialiteController::class, 'handleProviderCallback']);

require __DIR__ . '/auth.php';
require __DIR__ . '/api.php';
