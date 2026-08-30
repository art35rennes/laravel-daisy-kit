<?php

declare(strict_types=1);

namespace Art35rennes\DaisyKit;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class DaisyKitServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'daisy-kit');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'daisy-kit');
        Blade::anonymousComponentNamespace('daisy-kit::', 'daisy-kit');

        $this->registerAboutInformation();
    }

    private function registerAboutInformation(): void
    {
        if (! class_exists(AboutCommand::class)) {
            return;
        }

        AboutCommand::add('Daisy Kit', fn (): array => [
            'Version' => InstalledVersions::getPrettyVersion('art35rennes/laravel-daisy-kit') ?? 'development',
            'Blade namespace' => 'x-daisy-kit::',
            'Assets' => 'Explicit ESM/CSS module imports from dist/',
        ]);
    }
}
