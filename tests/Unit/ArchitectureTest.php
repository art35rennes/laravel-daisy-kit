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

it('ships concise Laravel Boost resources for package consumers', function (): void {
    $guideline = (string) file_get_contents(packagePath('resources/boost/guidelines/core.blade.php'));
    $skill = (string) file_get_contents(packagePath('resources/boost/skills/laravel-daisy-kit-development/SKILL.md'));

    expect($guideline)
        ->toContain('Laravel Daisy Kit')
        ->toContain('PHP 8.4')
        ->toContain('Laravel 13')
        ->toContain('x-daisy-kit::forms.viewer')
        ->toContain('`mount(root)`, `mountAll(scope = document)`, and `unmount(root)`')
        ->toContain('daisy-kit:{module}:*')
        ->toContain('CSP');

    expect($skill)
        ->toStartWith("---\nname: laravel-daisy-kit-development\n")
        ->toContain('DaisyUI and Tailwind CSS')
        ->toContain('Pest 5')
        ->toContain('Test Impact Analysis')
        ->toContain('laravel-best-practices')
        ->not->toMatch('/x-daisy::|daisy::|echarts|cally|calendar|codemirror|trix|gridstack|vendor:publish/i');
});
