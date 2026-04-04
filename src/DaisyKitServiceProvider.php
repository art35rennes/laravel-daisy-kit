<?php

namespace Art35rennes\DaisyKit;

use Art35rennes\DaisyKit\Http\Controllers\CsrfTokenController;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DaisyKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/daisy-kit.php', 'daisy-kit');
    }

    public function boot(): void
    {
        // Charger les vues du package avec un namespace: x-daisy::ui.button
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'daisy');
        // Exposer les templates comme composants anonymes pour éviter les doublons avec components/templates.
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/templates', 'daisy::templates');

        // Charger les traductions du package: __('daisy::calendar.today')
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'daisy');

        // Publication optionnelle des vues
        $this->publishes([
            __DIR__.'/../resources/views/components' => resource_path('views/vendor/daisy/components'),
        ], 'daisy-views');

        // Publication optionnelle des templates
        $this->publishes([
            __DIR__.'/../resources/views/templates' => resource_path('views/vendor/daisy/templates'),
        ], 'daisy-templates');

        // Publication optionnelle des traductions
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/daisy'),
        ], 'daisy-lang');

        // Publication optionnelle de la configuration
        $this->publishes([
            __DIR__.'/../config/daisy-kit.php' => config_path('daisy-kit.php'),
        ], 'daisy-config');

        // Publication optionnelle des sources d'assets (pour intégration manuelle dans le build du projet hôte)
        $this->publishes([
            __DIR__.'/../resources/js' => resource_path('vendor/daisy-kit/js'),
            __DIR__.'/../resources/css' => resource_path('vendor/daisy-kit/css'),
        ], 'daisy-assets-source');

        if (is_dir(__DIR__.'/../public/vendor/art35rennes/laravel-daisy-kit')) {
            $this->publishes([
                __DIR__.'/../public/vendor/art35rennes/laravel-daisy-kit' => public_path('vendor/art35rennes/laravel-daisy-kit'),
            ], 'daisy-assets');
        }

        // Alias conservé pour compatibilité.
        $this->publishes([
            __DIR__.'/../resources/js' => resource_path('vendor/daisy-kit/js'),
            __DIR__.'/../resources/css' => resource_path('vendor/daisy-kit/css'),
        ], 'daisy-src');

        if ((bool) config('daisy-kit.csrf_refresh.enabled', true)) {
            Route::middleware((array) config('daisy-kit.csrf_refresh.middleware', ['web']))->group(function () {
                Route::get((string) config('daisy-kit.csrf_refresh.path', 'daisy-kit/csrf-token.json'), [CsrfTokenController::class, '__invoke'])
                    ->name((string) config('daisy-kit.csrf_refresh.name', 'daisy-kit.csrf-token'));
            });
        }
    }
}
