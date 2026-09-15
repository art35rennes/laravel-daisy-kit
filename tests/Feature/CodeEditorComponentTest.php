<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Support\JsonConfiguration;

it('renders escaped code with native form semantics and inert configuration', function (): void {
    $payload = '</textarea><script>alert("code")</script>';
    $html = view('daisy-kit::components.code-editor', [
        'value' => $payload, 'name' => 'source', 'required' => true,
        'label' => 'Source code', 'nonce' => 'fixture-nonce',
    ])->render();

    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);
    $configuration = JsonConfiguration::decode(html_entity_decode($matches[1] ?? ''));

    expect($html)->toContain('data-daisy-kit-module="code-editor"', 'name="source"', 'required', e($payload))
        ->not->toContain($payload, 'style=', 'onclick=')
        ->and($configuration)->toMatchArray(['language' => 'text', 'nonce' => 'fixture-nonce', 'readOnly' => false]);
});

it('renders read-only and disabled code without requiring a form name', function (): void {
    $html = view('daisy-kit::components.code-editor', ['readOnly' => true, 'disabled' => true])->render();
    expect($html)->toContain('readonly', 'disabled')->not->toContain('name=');
});

it('serves the editor workbench with nonce styles and strict script attributes', function (): void {
    $response = $this->get('/code-editor')->assertOk()->assertSee('settings.json');
    expect($response->headers->get('Content-Security-Policy'))
        ->toContain("style-src 'self' 'nonce-", "style-src-attr 'none'", "script-src-attr 'none'");
});

it('translates toolbar, runtime feedback and CodeMirror phrases with per-instance overrides', function (): void {
    app()->setLocale('fr');
    $html = view('daisy-kit::components.code-editor', [
        'labels' => ['copy' => 'Copier le fichier'], 'phrases' => ['Find' => 'Chercher ici'],
    ])->render();
    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);
    $configuration = JsonConfiguration::decode(html_entity_decode($matches[1] ?? ''));

    expect($html)->toContain('Copier le fichier', 'Agrandir l’éditeur', 'data-daisy-kit-configuration-error=')
        ->and($configuration['labels'])->toMatchArray(['copy' => 'Copier le fichier', 'copied' => 'Copié', 'restore' => 'Réduire l’éditeur'])
        ->and($configuration['phrases'])->toMatchArray(['Find' => 'Chercher ici', 'Replace' => 'Remplacer', 'Completions' => 'Suggestions']);
});

it('renders only allowed toolbar actions and supports an empty toolbar menu', function (): void {
    $html = view('daisy-kit::components.code-editor', ['toolbarActions' => ['copy', 'expand', 'unknown']])->render();

    expect($html)->toContain('data-code-editor-action="copy"', 'data-code-editor-action="expand"')
        ->not->toContain('data-code-editor-action="search"', 'data-code-editor-action="unknown"');

    $empty = view('daisy-kit::components.code-editor', ['toolbarActions' => []])->render();
    expect($empty)->not->toContain('data-code-editor-action=');
});
