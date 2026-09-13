<?php

declare(strict_types=1);

it('serves ranked remote combobox options using the public response shape', function (): void {
    $this->getJson('/_daisy-kit-test/combobox/reviewers?query=grace')
        ->assertOk()
        ->assertExactJson([
            'items' => [[
                'value' => 'grace',
                'label' => 'Grace Hopper',
                'description' => 'grace@navy.mil',
                'initials' => 'GH',
                'meta' => 'Infrastructure',
            ]],
            'nextCursor' => null,
        ]);
});

it('serves initial rich Combobox suggestions without a search term', function (): void {
    $this->getJson('/_daisy-kit-test/combobox/reviewers?query=')
        ->assertOk()
        ->assertJsonCount(6, 'items')
        ->assertJsonPath('items.0.description', 'ada@analytical-engine.org')
        ->assertJsonPath('items.0.initials', 'AL')
        ->assertJsonPath('items.0.meta', 'Platform');
});

it('searches remote Combobox suggestions across e-mail domains', function (): void {
    $this->getJson('/_daisy-kit-test/combobox/reviewers?query=nasa.gov')
        ->assertOk()
        ->assertJsonCount(3, 'items')
        ->assertJsonPath('items.0.value', 'margaret')
        ->assertJsonPath('items.1.value', 'katherine')
        ->assertJsonPath('items.2.value', 'annie');
});

it('serves nested Tree search results from a local Laravel endpoint', function (): void {
    $this->getJson('/_daisy-kit-test/tree/search?query=api')
        ->assertOk()
        ->assertExactJson([
            'items' => [[
                'id' => 'documentation',
                'label' => 'Documentation',
                'expanded' => true,
                'children' => [[
                    'id' => 'api-reference',
                    'label' => 'API reference',
                ]],
            ]],
        ]);
});

it('accepts the native Workbench review form submission', function (): void {
    $this->post('/_daisy-kit-test/reviews', [
        'reviewers' => ['ada', 'grace'],
        'release_tags' => ['security', 'release-urgent'],
        'assignees' => ['margaret', 'ada'],
        'approval_signature' => 'data:image/png;base64,fixture',
    ])
        ->assertRedirect('/combobox')
        ->assertSessionHas('workbench.review.saved');
});

it('returns a native review submission to its originating Workbench module', function (): void {
    $this->post('/_daisy-kit-test/reviews', [
        'assignees' => ['ada', 'grace'],
        'return_to' => 'transfer-list',
    ])
        ->assertRedirect('/transfer-list')
        ->assertSessionHas('workbench.review.saved', [
            'assignees' => ['ada', 'grace'],
        ]);
});

it('serves dedicated strict and dependency-compatible CSP pages', function (): void {
    $strict = $this->get('/_daisy-kit-test/csp/strict')
        ->assertOk()
        ->assertSee('Daisy Kit strict CSP fixture');

    expect($strict->headers->get('Content-Security-Policy'))
        ->toContain("style-src-attr 'none'")
        ->not->toContain("style-src-attr 'unsafe-inline'");

    $relaxed = $this->get('/_daisy-kit-test/csp/dependency-styles')
        ->assertOk()
        ->assertSee('Daisy Kit dependency style CSP fixture');

    expect($relaxed->headers->get('Content-Security-Policy'))
        ->toContain("style-src-attr 'unsafe-inline'");

    $this->get('/_daisy-kit-test/csp/map')
        ->assertSee('data-workbench-module="map"', false);
    $this->get('/_daisy-kit-test/csp/file-preview')
        ->assertSee('data-workbench-module="file-preview"', false);
});
