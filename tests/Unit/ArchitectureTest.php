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
        'combobox',
        'copyable',
        'file-preview',
        'map',
        'scrollspy',
        'signature',
        'table',
        'transfer-list',
        'tree',
        'truncate',
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

it('requires PHP 8.4 without a Forms or Livewire integration', function (): void {
    $composer = json_decode((string) file_get_contents(packagePath('composer.json')), true, 512, JSON_THROW_ON_ERROR);

    expect($composer['require']['php'])->toBe('^8.4')
        ->and($composer['require'])->not->toHaveKey('livewire/livewire')
        ->and($composer)->not->toHaveKey('suggest');
});

it('ships concise Laravel Boost resources for package consumers', function (): void {
    $guideline = (string) file_get_contents(packagePath('resources/boost/guidelines/core.blade.php'));
    $skill = (string) file_get_contents(packagePath('resources/boost/skills/laravel-daisy-kit-development/SKILL.md'));

    expect($guideline)
        ->toContain('Laravel Daisy Kit')
        ->toContain('PHP 8.4')
        ->toContain('Laravel 13')
        ->toContain('x-daisy-kit::copyable')
        ->toContain('`mount(root)`, `mountAll(scope = document)`, `unmount(root)`, and `getInstance(root)`')
        ->toContain('daisy-kit:{module}:*')
        ->toContain('CSP')
        ->toContain('v5-product-contract-matrix.md');

    expect($skill)
        ->toStartWith("---\nname: laravel-daisy-kit-development\n")
        ->toContain('DaisyUI and Tailwind CSS')
        ->toContain('Pest 5')
        ->toContain('Test Impact Analysis')
        ->toContain('laravel-best-practices')
        ->toContain('v5-product-contract-matrix.md')
        ->toContain('crypto.randomUUID()')
        ->not->toMatch('/x-daisy::|daisy::|echarts|cally|calendar|codemirror|\\btrix\\b|gridstack|vendor:publish/i');
});

it('documents the Vite alias for Composer-installed module entries', function (): void {
    $documentation = collect([
        packagePath('AGENTS.md'),
        packagePath('README.md'),
        packagePath('docs/decisions/0003-vite-composer-alias.md'),
        packagePath('docs/specs/v5-public-contract.md'),
        packagePath('resources/boost/guidelines/core.blade.php'),
        packagePath('resources/boost/skills/laravel-daisy-kit-development/SKILL.md'),
    ])->mapWithKeys(fn (string $path): array => [$path => (string) file_get_contents($path)]);

    $nonResolvableNpmSpecifier = 'art35rennes/'.'laravel-daisy-kit/dist';
    $fakeNpmImport = '/(?:from\\s+|import\\s*(?:\\(\\s*)?)[\'\"]'.preg_quote($nonResolvableNpmSpecifier, '/').'/';

    expect($documentation->get(packagePath('README.md')))
        ->toContain("import { resolve } from 'node:path';")
        ->toContain("import { fileURLToPath } from 'node:url';")
        ->toContain("const __dirname = fileURLToPath(new URL('.', import.meta.url));")
        ->toContain("'@daisy-kit': resolve(__dirname, 'vendor/art35rennes/laravel-daisy-kit/dist'),")
        ->toContain("import '@daisy-kit/table.css';")
        ->toContain("from '@daisy-kit/table.js'");

    expect($documentation->implode("\n"))
        ->toContain('@daisy-kit/tree.js')
        ->toContain('@daisy-kit/blueprint.css')
        ->toContain('@daisy-kit/file-preview.js')
        ->toContain('@daisy-kit/map.css')
        ->toContain('@daisy-kit/copyable.js')
        ->toContain('@daisy-kit/transfer-list.css')
        ->not->toMatch($fakeNpmImport);
});

it('documents the corrective development contract with copyable examples for every module', function (): void {
    $readme = (string) file_get_contents(packagePath('README.md'));
    $examples = (string) file_get_contents(packagePath('docs/examples.md'));
    $contract = (string) file_get_contents(packagePath('docs/specs/v5-public-contract.md'));
    $dependencies = (string) file_get_contents(packagePath('docs/dependencies.md'));

    expect($readme)
        ->toContain('v5.1.0-alpha.2')
        ->not->toContain('v5.0.0-alpha.2');

    expect($readme)
        ->toMatch('/validation propriétaire en\\s+attente/')
        ->toMatch('/v5\\.0\\.0 or its historical\\s+alpha releases/');

    expect($examples)
        ->toContain("'@daisy-kit': resolve(__dirname, 'vendor/art35rennes/laravel-daisy-kit/dist'),")
        ->toContain('x-daisy-kit::table')
        ->toContain('x-daisy-kit::tree')
        ->toContain('x-daisy-kit::blueprint')
        ->toContain('x-daisy-kit::file-preview')
        ->toContain('x-daisy-kit::map')
        ->toContain('x-daisy-kit::copyable')
        ->toContain('x-daisy-kit::combobox')
        ->toContain('x-daisy-kit::signature')
        ->toContain('x-daisy-kit::truncate')
        ->toContain('x-daisy-kit::scrollspy')
        ->toContain('x-daisy-kit::transfer-list')
        ->not->toMatch('/x-daisy::|daisy::/');

    $fakeNpmImport = '/(?:from\\s+|import\\s*(?:\\(\\s*)?)[\'\"]art35rennes\\/laravel-daisy-kit\\/dist/';

    expect($examples)->not->toMatch($fakeNpmImport);

    expect($contract)
        ->toMatch('/v5\\.0\\.0 or its\\s+historical alpha releases/');

    expect($dependencies)
        ->toContain('@tanstack/table-core | 9.2.3')
        ->toContain('Laravel Boost | 2.7.0')
        ->toContain('Official source');
});

it('keeps File Preview frame helpers relative to its Vite entry', function (): void {
    $entry = (string) file_get_contents(packagePath('resources/js/file-preview.js'));
    $frameDocument = (string) file_get_contents(packagePath('resources/js/file-preview/frame-document.js'));
    $distribution = (string) file_get_contents(packagePath('dist/file-preview.js'));

    expect($entry)
        ->toContain("'./file-preview/frame-document.js'")
        ->not->toContain('file-preview-frame-bootstrap')
        ->not->toContain("'/file-preview-frame.html'")
        ->and($frameDocument)
        ->toContain("new URL('../../../.tmp/file-preview-frame/file-preview-frame.js', import.meta.url)")
        ->toContain("new URL('../../../.tmp/file-preview-frame/file-preview-frame.css', import.meta.url)")
        ->not->toContain('file-preview-frame-bootstrap')
        ->not->toContain("'/file-preview-frame.html'");

    expect($distribution)
        ->toContain('file-preview-frame.js')
        ->not->toContain('file-preview-frame-bootstrap')
        ->not->toContain('/file-preview-frame.html')
        ->not->toContain('data:text/javascript');
});
