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

        // Charger les vues de dev (pages de démo/templates) sans les publier par défaut
        $devViews = __DIR__.'/../resources/dev/views';
        if (is_dir($devViews)) {
            $this->loadViewsFrom($devViews, 'daisy-dev');
        }

        // Charger les traductions du package: __('daisy::calendar.today')
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'daisy');

        // Publication optionnelle des vues
        $this->publishes([
            __DIR__.'/../resources/views/components' => resource_path('views/vendor/daisy/components'),
        ], 'daisy-views');

        // Publication optionnelle des traductions
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/daisy'),
        ], 'daisy-lang');
    }
}

