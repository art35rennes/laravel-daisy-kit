<?php

use App\Helpers\TemplateScanner;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    TemplateScanner::clearCache();
});

afterEach(function () {
    TemplateScanner::clearCache();

    $testFile = resource_path('views/templates/auth/__scanner-test-template.blade.php');
    if (File::exists($testFile)) {
        File::delete($testFile);
    }

    $defaultTypeFile = resource_path('views/templates/auth/__scanner-test-default-type.blade.php');
    if (File::exists($defaultTypeFile)) {
        File::delete($defaultTypeFile);
    }
});

it('builds cache and extracts annotations from templates', function () {
    $testFile = resource_path('views/templates/auth/__scanner-test-template.blade.php');
    File::ensureDirectoryExists(dirname($testFile));

    File::put($testFile, <<<'BLADE'
{{-- @template-label Scanner Test Template --}}
{{-- @template-description A template used for scanner tests. --}}
{{-- @template-tags alpha, beta --}}
{{-- @template-type reusable --}}
{{-- @template-route templates.auth.login-simple --}}

<x-daisy::layout.app title="Test">
    <div>Test</div>
</x-daisy::layout.app>
BLADE);

    $inventory = TemplateScanner::rebuildCache();

    expect($inventory)
        ->toHaveKey('generated_at')
        ->toHaveKey('files_hash')
        ->toHaveKey('templates')
        ->toHaveKey('categories');

    $templates = collect($inventory['templates'] ?? []);
    $template = $templates->firstWhere('name', '__scanner-test-template');

    expect($template)->not->toBeNull()
        ->and($template['category'])->toBe('auth')
        ->and($template['label'])->toBe('Scanner Test Template')
        ->and($template['description'])->toBe('A template used for scanner tests.')
        ->and($template['type'])->toBe('reusable')
        ->and($template['route'])->toBe('templates.auth.login-simple')
        ->and($template)->toHaveKey('component');

    expect(TemplateScanner::isCacheValid())->toBeTrue();
});

it('defaults template type to reusable for auth category when not annotated', function () {
    $testFile = resource_path('views/templates/auth/__scanner-test-default-type.blade.php');
    File::ensureDirectoryExists(dirname($testFile));

    File::put($testFile, <<<'BLADE'
<div>Auth default type template</div>
BLADE);

    $inventory = TemplateScanner::rebuildCache();
    $templates = collect($inventory['templates'] ?? []);
    $template = $templates->firstWhere('name', '__scanner-test-default-type');

    expect($template)->not->toBeNull()
        ->and($template['type'])->toBe('reusable');
});
