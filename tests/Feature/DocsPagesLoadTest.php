<?php

use App\Helpers\DocsHelper;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::set('daisy-kit.docs.enabled', true);
    $this->prefix = config('daisy-kit.docs.prefix', 'docs');
});

it('loads the docs index page without errors', function () {
    $response = $this->get("/{$this->prefix}");

    $response->assertSuccessful();
    $response->assertSee('Documentation', false);
    $response->assertDontSee('syntax error', false);
    $response->assertDontSee('unexpected identifier', false);
    $response->assertDontSee('Parse error', false);
});

it('loads the templates index page without errors', function () {
    $response = $this->get("/{$this->prefix}/templates");

    $response->assertSuccessful();
    $response->assertSee('Templates', false);
    $response->assertDontSee('syntax error', false);
    $response->assertDontSee('unexpected identifier', false);
    $response->assertDontSee('Parse error', false);
});

it('loads all component documentation pages without errors', function () {
    $componentsByCategory = DocsHelper::getComponentsByCategory($this->prefix);
    $pages = [];

    foreach ($componentsByCategory as $category) {
        foreach ($category['components'] ?? [] as $component) {
            $href = $component['href'] ?? '';
            if (empty($href)) {
                continue;
            }

            $path = parse_url($href, PHP_URL_PATH);
            if ($path === null) {
                continue;
            }

            $pages[] = $path;
        }
    }

    expect($pages)->not->toBeEmpty();

    foreach ($pages as $path) {
        $response = $this->get($path);

        $response->assertSuccessful();

        $content = $response->getContent();

        // Verify there are no PHP errors shown in the rendered HTML.
        expect($content)
            ->not->toContain('syntax error', false)
            ->not->toContain('unexpected identifier', false)
            ->not->toContain('Parse error', false)
            ->not->toContain('Fatal error', false)
            ->not->toContain('Call to undefined', false);
    }
})->group('docs');

it('loads all template documentation pages without errors', function () {
    $navItems = DocsHelper::getTemplateNavigationItems($this->prefix);
    $pages = [];

    foreach ($navItems as $category) {
        foreach ($category['children'] ?? [] as $template) {
            $href = $template['href'] ?? '';
            if (empty($href)) {
                continue;
            }

            $path = parse_url($href, PHP_URL_PATH);
            if ($path === null) {
                continue;
            }

            $pages[] = $path;
        }
    }

    expect($pages)->not->toBeEmpty();

    foreach ($pages as $path) {
        $response = $this->get($path);

        $response->assertSuccessful();

        $content = $response->getContent();

        // Verify there are no PHP errors shown in the rendered HTML.
        expect($content)
            ->not->toContain('syntax error', false)
            ->not->toContain('unexpected identifier', false)
            ->not->toContain('Parse error', false)
            ->not->toContain('Fatal error', false)
            ->not->toContain('Call to undefined', false);
    }
})->group('docs');
