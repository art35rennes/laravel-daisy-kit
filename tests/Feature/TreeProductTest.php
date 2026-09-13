<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Support\JsonConfiguration;
use Illuminate\View\ViewException;

function treeConfig(array $options): array
{
    $html = view('daisy-kit::components.tree', $options)->render();
    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);

    return JsonConfiguration::decode($matches[1]);
}

it('renders initial selections and respects the exact field name and disabled state', function (): void {
    $html = view('daisy-kit::components.tree', [
        'items' => [['id' => 12, 'label' => 'Legal']], 'value' => 12, 'name' => 'area', 'disabled' => true,
    ])->render();

    expect($html)->toContain('name="area"')->toContain('value="[&quot;12&quot;]"')
        ->toContain('disabled')->not->toContain('name="area[]"');
    expect(treeConfig(['value' => null])['hasInitialValue'])->toBeTrue();
    expect(treeConfig([])['hasInitialValue'])->toBeFalse();
});

it('supports translated manual search and per-instance labels', function (): void {
    app()->setLocale('fr');
    $html = view('daisy-kit::components.tree', ['searchable' => true, 'searchMode' => 'manual', 'labels' => ['clear' => 'Annuler mon choix']])->render();

    expect($html)->toContain('Rechercher dans l’arborescence')->toContain('Annuler mon choix')
        ->toContain('data-tree-command="applySearch"')->not->toContain('Search tree');
});

it('serializes opt-in match highlighting as CSP-safe configuration', function (): void {
    expect(treeConfig(['highlightMatches' => true]))->toMatchArray(['highlightMatches' => true]);
});

it('rejects ambiguous or unsafe tree configuration', function (array $options): void {
    expect(fn () => view('daisy-kit::components.tree', $options)->render())->toThrow(ViewException::class);
})->with([
    [['valueMode' => 'all']], [['searchMode' => 'invalid']], [['searchMatch' => 'invalid']],
    [['searchDebounce' => -1]], [['searchMin' => -1]], [['searchParam' => 'q&evil']],
    [['multiple' => 'yes']], [['highlightMatches' => 'yes']], [['items' => [['id' => 'a', 'label' => 'A'], ['id' => 'a', 'label' => 'B']]]],
    [['items' => [['label' => 'No id']]]], [['items' => [['id' => 'a', 'label' => 'A', 'source' => 'javascript:alert(1)']]]],
    [['searchSource' => 'data:text/html,evil']], [['labels' => ['clear' => []]]],
    [['initialExpandPaths' => ['not-a-path']]], [['multiple' => true, 'value' => 'a']],
]);

it('renders custom Blade presentation into an inert node template with escaped data', function (): void {
    view()->addNamespace('tree-test', __DIR__.'/../Fixtures/views');
    $html = view('daisy-kit::components.tree', [
        'items' => [['id' => 'team', 'label' => '<img src=x onerror=alert(1)>', 'badge' => 'Platform']],
        'nodeView' => 'tree-test::tree-node',
    ])->render();

    expect($html)->toContain('<template data-daisy-kit-tree-template="team">')
        ->toContain('badge badge-ghost')->toContain('&lt;img')->not->toContain('<img');
});

it('rejects custom node templates that take over controls or execute code', function (): void {
    view()->addNamespace('tree-test', __DIR__.'/../Fixtures/views');
    expect(fn () => view('daisy-kit::components.tree', [
        'items' => [['id' => 'a', 'label' => 'A']], 'nodeView' => 'tree-test::tree-node-unsafe',
    ])->render())->toThrow(ViewException::class);
});
