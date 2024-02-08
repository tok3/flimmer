<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Js;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        /*        FilamentAsset::register([
                    Js::make('masonry', resource_path('js/masonry.pkgd.min.js')),
                ]);*/


        FilamentAsset::register([
            Js::make('masonry', resource_path('js/masonry.pkgd.min.js')),
            Js::make('twilio', 'https://sdk.twilio.com/js/video/releases/2.26.1/twilio-video.min.js'),
            Js::make('video-hand-raise', resource_path('js/video-hand-raise.js'))->loadedOnRequest(),
            //Js::make('token', resource_path('js/token.js')),

        ]);


    }
}
