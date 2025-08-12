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

        // Charger les traductions du package: __('daisy::calendar.today')
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'daisy');

        // Publication optionnelle des vues
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/daisy'),
        ], 'daisy-views');

        // Publication optionnelle des traductions
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/daisy'),
        ], 'daisy-lang');
    }
}

