<?php

namespace Art35rennes\DaisyKit;

use Art35rennes\DaisyKit\FormKit\Livewire\FormBuilder;
use Art35rennes\DaisyKit\Http\Controllers\CsrfTokenController;
use Art35rennes\DaisyKit\Support\PackagePaths;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use Livewire\Livewire;
use Throwable;

class DaisyKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(PackagePaths::config(), 'daisy-kit');

        $this->callAfterResolving(Handler::class, function (Handler $handler): void {
            $handler->map(InvalidArgumentException::class, function (InvalidArgumentException $exception) {
                return $this->mapMissingDaisyComponentException($exception);
            });
        });
    }

    public function boot(): void
    {
        // Charger les vues du package avec un namespace: x-daisy::ui.button
        $this->loadViewsFrom(PackagePaths::path('resources', 'views'), 'daisy');
        // Exposer les vues du package comme composants anonymes sans miroirs components/templates.
        Blade::anonymousComponentNamespace('daisy::', 'daisy');

        if (class_exists(Livewire::class)) {
            Livewire::component('daisy.form-builder', FormBuilder::class);
        }

        // Charger les traductions du package: __('daisy::calendar.today')
        $this->loadTranslationsFrom(PackagePaths::lang(), 'daisy');

        // Publication optionnelle des vues
        $this->publishes([
            PackagePaths::viewsComponents() => resource_path('views/vendor/daisy/components'),
        ], 'daisy-views');

        // Publication optionnelle des templates
        $this->publishes([
            PackagePaths::viewsTemplates() => resource_path('views/vendor/daisy/templates'),
        ], 'daisy-templates');

        // Publication optionnelle des traductions
        $this->publishes([
            PackagePaths::lang() => resource_path('lang/vendor/daisy'),
        ], 'daisy-lang');

        // Publication optionnelle de la configuration
        $this->publishes([
            PackagePaths::config() => config_path('daisy-kit.php'),
        ], 'daisy-config');

        // Publication optionnelle des sources d'assets (pour intégration manuelle dans le build du projet hôte)
        $this->publishes([
            PackagePaths::js() => resource_path('vendor/daisy-kit/js'),
            PackagePaths::css() => resource_path('vendor/daisy-kit/css'),
        ], 'daisy-assets-source');

        $this->publishes([
            PackagePaths::distributableAssets() => public_path('vendor/art35rennes/laravel-daisy-kit'),
        ], 'daisy-assets');

        // Alias conservé pour compatibilité.
        $this->publishes([
            PackagePaths::js() => resource_path('vendor/daisy-kit/js'),
            PackagePaths::css() => resource_path('vendor/daisy-kit/css'),
        ], 'daisy-src');

        if ((bool) config('daisy-kit.csrf_refresh.enabled', true)) {
            Route::middleware((array) config('daisy-kit.csrf_refresh.middleware', ['web']))->group(function () {
                Route::get((string) config('daisy-kit.csrf_refresh.path', 'daisy-kit/csrf-token.json'), [CsrfTokenController::class, '__invoke'])
                    ->name((string) config('daisy-kit.csrf_refresh.name', 'daisy-kit.csrf-token'));
            });
        }
    }

    protected function mapMissingDaisyComponentException(InvalidArgumentException $exception): InvalidArgumentException
    {
        if (! preg_match('/Unable to locate a class or view for component \[(daisy::[^\]]+)\]\./', $exception->getMessage(), $matches)) {
            return $exception;
        }

        $component = $matches[1];
        $componentKey = preg_replace('/^daisy::/', '', $component);
        $packageView = PackagePaths::path('resources', 'views', 'components', ...explode('.', $componentKey)).'.blade.php';

        if (! is_string($componentKey) || ! is_file($packageView)) {
            return $exception;
        }

        $publishedView = resource_path('views/vendor/daisy/components/'.str_replace('.', '/', $componentKey).'.blade.php');
        $packageView = str_replace('\\', '/', $packageView);
        $publishedView = str_replace('\\', '/', $publishedView);

        $message = $exception->getMessage().' DaisyKit fournit bien ce composant, donc cela ressemble a un probleme de vues publiees obsoletes ou incompletes.'
            .' Si vous avez publie les vues du package, republiez-les avec `php artisan vendor:publish --tag=daisy-views --force`'
            .' puis videz le cache Blade avec `php artisan view:clear`.'
            .' Vue package attendue: '.$packageView.'.'
            .' Vue publiee attendue: '.$publishedView.'.';

        return new InvalidArgumentException($message, (int) $exception->getCode(), $exception instanceof Throwable ? $exception : null);
    }
}
