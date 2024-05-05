<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (App::environment('production')) {
            URL::forceScheme('https');
        }


        Socialite::extend('osm', function ($app) {
            $config = $app['config']['services.osm'];

            return Socialite::buildProvider(OsmProvider::class, $config);
        });
    }
}

