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

// Dataset pour les pages de composants
$componentPages = function () {
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $componentsByCategory = DocsHelper::getComponentsByCategory($prefix);
    $pages = [];
    
    foreach ($componentsByCategory as $categoryId => $category) {
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
    
    return $pages;
};

// Dataset pour les pages de templates
$templatePages = function () {
    $prefix = config('daisy-kit.docs.prefix', 'docs');
    $navItems = DocsHelper::getTemplateNavigationItems($prefix);
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
    
    return $pages;
};

it('loads component documentation page without errors', function (string $path) {
    $response = $this->get($path);
    
    $response->assertSuccessful();
    
    $content = $response->getContent();
    
    // Vérifier qu'il n'y a pas d'erreurs de syntaxe PHP
    expect($content)
        ->not->toContain('syntax error', false)
        ->not->toContain('unexpected identifier', false)
        ->not->toContain('Parse error', false)
        ->not->toContain('Fatal error', false)
        ->not->toContain('Call to undefined', false);
})->with($componentPages)->group('docs');

it('loads template documentation page without errors', function (string $path) {
    $response = $this->get($path);
    
    $response->assertSuccessful();
    
    $content = $response->getContent();
    
    // Vérifier qu'il n'y a pas d'erreurs de syntaxe PHP
    expect($content)
        ->not->toContain('syntax error', false)
        ->not->toContain('unexpected identifier', false)
        ->not->toContain('Parse error', false)
        ->not->toContain('Fatal error', false)
        ->not->toContain('Call to undefined', false);
})->with($templatePages)->group('docs');
