<?php

declare(strict_types=1);

use Workbench\App\TreeExamples;

it('renders four realistic tree selectors under strict CSP', function (): void {
    $response = $this->get('/tree?lang=fr&theme=dark');
    $response->assertOk()->assertSee('data-theme="dark"', false)
        ->assertSee('Rechercher dans l’arborescence')
        ->assertSee('data-daisy-kit-tree-template="platform-0"', false);
    expect(substr_count($response->getContent(), 'data-daisy-kit-module="tree"'))->toBe(4);
    expect($response->headers->get('Content-Security-Policy'))->toContain("script-src-attr 'none'", "style-src-attr 'none'");
    foreach ([TreeExamples::classification(), TreeExamples::permissions(), TreeExamples::teams()] as $items) {
        $count = function (array $nodes) use (&$count): int {
            return count($nodes) + array_sum(array_map(fn (array $node): int => $count($node['children'] ?? []), $nodes));
        };
        expect($count($items))->toBeGreaterThanOrEqual(25);
    }
});

it('serves independently paginated lazy branches and ancestor-preserving search', function (): void {
    $this->get('/_daisy-kit-test/tree/catalogue/west')->assertOk()
        ->assertJsonCount(2, 'items')->assertJsonPath('nextCursor', '2');
    $this->get('/_daisy-kit-test/tree/catalogue/west?cursor=2')->assertOk()
        ->assertJsonCount(2, 'items')->assertJsonPath('items.0.id', 'west-3')->assertJsonPath('nextCursor', '4');
    $this->get('/_daisy-kit-test/tree/catalogue/north')->assertOk()
        ->assertJsonCount(2, 'items')->assertJsonPath('items.0.id', 'north-1');
    $this->get('/_daisy-kit-test/tree/catalogue-search?query=North')->assertOk()->assertJsonPath('items.0.id', 'north');
    $this->get('/_daisy-kit-test/tree/catalogue/unknown')->assertNotFound();
});
