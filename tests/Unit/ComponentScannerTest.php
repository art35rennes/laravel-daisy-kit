<?php

use App\Helpers\ComponentScanner;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    ComponentScanner::clearCache();
});

afterEach(function () {
    ComponentScanner::clearCache();

    $testFile = resource_path('views/components/ui/inputs/__scanner-test-component.blade.php');
    if (File::exists($testFile)) {
        File::delete($testFile);
    }
});

it('builds cache and extracts metadata from blade components', function () {
    $testFile = resource_path('views/components/ui/inputs/__scanner-test-component.blade.php');
    File::ensureDirectoryExists(dirname($testFile));

    File::put($testFile, <<<'BLADE'
@props([
    'foo' => null,
    'bar' => 'baz',
])

<div data-module="{{ $module ?? 'treeview' }}" data-hello="world">
    {{ $slot }}
</div>
BLADE);

    $inventory = ComponentScanner::rebuildCache();

    expect($inventory)
        ->toHaveKey('generated_at')
        ->toHaveKey('files_hash')
        ->toHaveKey('components');

    $components = collect($inventory['components'] ?? []);
    $component = $components->firstWhere('name', '__scanner-test-component');

    expect($component)->not->toBeNull()
        ->and($component['category'])->toBe('inputs')
        ->and($component['jsModule'])->toBe('treeview')
        ->and($component['props'])->toContain('foo')
        ->and($component['props'])->toContain('bar')
        ->and($component['dataAttributes'])->toContain('hello');

    expect(ComponentScanner::isCacheValid())->toBeTrue();
});

it('marks cache as stale after a watched file changes', function () {
    $testFile = resource_path('views/components/ui/inputs/__scanner-test-component.blade.php');
    File::ensureDirectoryExists(dirname($testFile));

    File::put($testFile, <<<'BLADE'
<div data-module="popover">Test</div>
BLADE);

    ComponentScanner::rebuildCache();
    expect(ComponentScanner::isCacheValid())->toBeTrue();

    // Ensure mtime changes across platforms.
    touch($testFile, time() + 5);

    expect(ComponentScanner::isCacheValid())->toBeFalse();
    expect(fn () => ComponentScanner::readCached())->toThrow(RuntimeException::class);
});
