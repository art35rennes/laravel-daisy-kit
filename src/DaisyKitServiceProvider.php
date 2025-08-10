<?php

namespace Art35rennes\DaisyKit;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class DaisyKitServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Charger les vues du package avec un namespace: x-daisy::ui.button
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'daisy');

        // Publication optionnelle des vues
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/daisy'),
        ], 'daisy-views');
    }
}

