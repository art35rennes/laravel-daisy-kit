<?php

declare(strict_types=1);

function packagePath(string $path = ''): string
{
    return dirname(__DIR__, 2).($path === '' ? '' : "/{$path}");
}

it('exposes only the v5 Blade component allowlist', function (): void {
    $components = collect([
        ...(glob(packagePath('resources/views/components/*.blade.php')) ?: []),
        ...(glob(packagePath('resources/views/components/*/*.blade.php')) ?: []),
    ])
        ->map(fn (string $path): string => str($path)
            ->after(packagePath('resources/views/components/'))
            ->replace('.blade.php', '')
            ->toString())
        ->sort()
        ->values()
        ->all();

    expect($components)->toBe([
        'blueprint',
        'file-preview',
        'forms/builder',
        'forms/viewer',
        'map',
        'table',
        'tree',
    ]);
});

it('does not retain legacy runtime systems', function (): void {
    $files = collect([
        ...(glob(packagePath('src/*.php')) ?: []),
        ...(glob(packagePath('src/*/*.php')) ?: []),
        ...(glob(packagePath('resources/views/components/*.blade.php')) ?: []),
        ...(glob(packagePath('resources/views/components/*/*.blade.php')) ?: []),
        ...(glob(packagePath('resources/js/*.js')) ?: []),
        ...(glob(packagePath('resources/js/*/*.js')) ?: []),
    ]);
    $contents = $files->map(fn (string $path): string => (string) file_get_contents($path))->implode("\n");

    expect($contents)->not->toMatch('/x-daisy::|daisy::|echarts|cally|calendar|codemirror|trix|gridstack|vendor:publish/i');
});

it('requires PHP 8.4 and keeps Livewire optional', function (): void {
    $composer = json_decode((string) file_get_contents(packagePath('composer.json')), true, 512, JSON_THROW_ON_ERROR);

    expect($composer['require']['php'])->toBe('^8.4')
        ->and($composer['require'])->not->toHaveKey('livewire/livewire')
        ->and($composer['suggest'])->toHaveKey('livewire/livewire');
});
