<?php

declare(strict_types=1);

namespace Art35rennes\DaisyKit;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class DaisyKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/daisy-kit.php', 'daisy-kit');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'daisy-kit');
        Blade::anonymousComponentNamespace('daisy-kit::', 'daisy-kit');

        $this->registerLivewireBuilder();
        $this->registerAboutInformation();
    }

    private function registerLivewireBuilder(): void
    {
        if (! config('daisy-kit.livewire_builder', true)) {
            return;
        }

        if (! class_exists(Livewire::class)) {
            return;
        }

        if (! InstalledVersions::isInstalled('livewire/livewire')) {
            return;
        }

        if (! str_starts_with(InstalledVersions::getPrettyVersion('livewire/livewire') ?? '', '4.')) {
            return;
        }

        Livewire::component('daisy-kit.forms.builder', Livewire\FormsBuilder::class);
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
            'Livewire builder' => $this->hasLivewireFour() ? 'available' : 'not installed',
        ]);
    }

    private function hasLivewireFour(): bool
    {
        if (! InstalledVersions::isInstalled('livewire/livewire')) {
            return false;
        }

        return str_starts_with(InstalledVersions::getPrettyVersion('livewire/livewire') ?? '', '4.');
    }
}
