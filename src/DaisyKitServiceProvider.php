<?php

namespace Art35rennes\DaisyKit;

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

        // Publication optionnelle des templates
        $this->publishes([
            __DIR__.'/../resources/views/templates' => resource_path('views/vendor/daisy/templates'),
        ], 'daisy-templates');

        // Publication optionnelle des vues de démo/docs vivantes
        $this->publishes([
            __DIR__.'/../resources/dev/views' => resource_path('views/vendor/daisy-dev'),
        ], 'daisy-dev-views');

        // Publication optionnelle des traductions
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/daisy'),
        ], 'daisy-lang');

        // Publication optionnelle de la configuration
        $this->publishes([
            __DIR__.'/../config/daisy-kit.php' => config_path('daisy-kit.php'),
        ], 'daisy-config');

        // Publication optionnelle des sources d'assets (pour intégration dans le build du projet hôte)
        $this->publishes([
            __DIR__.'/../resources/js' => resource_path('vendor/daisy-kit/js'),
            __DIR__.'/../resources/css' => resource_path('vendor/daisy-kit/css'),
        ], 'daisy-src');

        // Enregistrer (optionnellement) les routes de documentation publiques du package
        $docsEnabled = (bool) config('daisy-kit.docs.enabled', false);
        if ($docsEnabled) {
            $prefix = (string) config('daisy-kit.docs.prefix', 'docs');
            Route::middleware('web')->prefix($prefix)->group(function () {
                // Accueil docs
                Route::get('/', function () {
                    return view('daisy-dev::docs.index');
                })->name('daisy.docs.index');

                // Page Templates
                Route::get('/templates', function () {
                    return view('daisy-dev::docs.templates.index');
                })->name('daisy.docs.templates');

                // Pages Composants /{category}/{component}
                Route::get('/{category}/{component}', function (string $category, string $component) {
                    $view = "daisy-dev::docs.components.$category.$component";
                    if (view()->exists($view)) {
                        return view($view, ['category' => $category, 'component' => $component]);
                    }
                    abort(404);
                })->where(['category' => '[A-Za-z0-9\-_]+', 'component' => '[A-Za-z0-9\-_]+'])
                    ->name('daisy.docs.component');
            });
        }
    }
}
