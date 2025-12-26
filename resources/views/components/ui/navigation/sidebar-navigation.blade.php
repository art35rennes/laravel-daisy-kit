@props([
    /**
     * items: [
     *   ['label' => 'Inputs', 'children' => [
     *      ['label' => 'Button', 'href' => '/docs/inputs/button'],
     *   ]],
     * ]
     */
    'items' => [],
    'current' => null,
    // Activer le filtrage du menu
    'searchable' => true,
    // Placeholder pour le champ de recherche
    'searchPlaceholder' => 'Rechercher...',
    // Module override
    'module' => 'menu-filter',
])

@php
    $currentPath = '/'.ltrim((string) ($current ?? request()->path()), '/');

    $isActive = function (?string $href) use ($currentPath): bool {
        if (!$href) {
            return false;
        }

        // Normaliser les chemins (supprimer les slashes finaux)
        $normalizedHref = rtrim($href, '/');
        $normalizedCurrent = rtrim($currentPath, '/');

        // Match exact
        if ($normalizedCurrent === $normalizedHref) {
            return true;
        }

        // Match avec sous-chemin : vérifier que le href est un préfixe exact (suivi de / ou fin de chaîne)
        if (str_starts_with($normalizedCurrent, $normalizedHref)) {
            $nextChar = substr($normalizedCurrent, strlen($normalizedHref), 1);
            // Le caractère suivant doit être '/' ou la fin de la chaîne
            return $nextChar === '' || $nextChar === '/';
        }

        return false;
    };

    $hasActive = function (array $node) use (&$hasActive, $isActive): bool {
        $href = $node['href'] ?? null;
        if (is_string($href) && $isActive($href)) {
            return true;
        }

        foreach (($node['children'] ?? []) as $child) {
            if (is_array($child) && $hasActive($child)) {
                return true;
            }
        }

        return false;
    };
@endphp

<nav aria-label="Navigation de la documentation" class="w-full">
    @if($searchable)
        <div class="mb-4" data-module="{{ $module }}">
            <input 
                type="text" 
                data-menu-filter-input
                placeholder="{{ $searchPlaceholder }}"
                class="input input-sm w-full"
                aria-label="Rechercher dans le menu"
            />
        </div>
    @endif
    @php
        $menuAttributes = $searchable ? ['data-menu-filter-target' => ''] : [];
    @endphp
    <x-daisy::ui.navigation.menu 
        :bg="false" 
        :rounded="false" 
        size="sm" 
        class="w-full"
        :attributes="new \Illuminate\View\ComponentAttributeBag($menuAttributes)"
    >
        @foreach($items as $node)
            @php
                $label = (string) ($node['label'] ?? '');
                $href = isset($node['href']) && is_string($node['href']) ? $node['href'] : null;
                $children = is_array($node['children'] ?? null) ? $node['children'] : [];
                $nodeHasChildren = !empty($children);

                $nodeIsActive = $isActive($href);
                $nodeHasActive = $nodeHasChildren ? $hasActive($node) : $nodeIsActive;
            @endphp

            @if($nodeHasChildren)
                <li class="w-full">
                    <details {{ $nodeHasActive ? 'open' : '' }} class="w-full">
                        <summary class="text-sm font-medium opacity-70 w-full cursor-pointer py-1.5 px-2">{{ $label }}</summary>
                        <x-daisy::ui.navigation.menu :bg="false" :rounded="false" size="xs" class="pl-2 w-full mt-1">
                            @foreach($children as $child)
                                @php
                                    $childLabel = (string) ($child['label'] ?? '');
                                    $childHref = isset($child['href']) && is_string($child['href']) ? $child['href'] : null;
                                    $childIsActive = $isActive($childHref);
                                @endphp

                                <li class="w-full">
                                    @if($childHref)
                                        <a href="{{ $childHref }}" class="block w-full {{ $childIsActive ? 'menu-active font-semibold' : '' }}">{{ $childLabel }}</a>
                                    @else
                                        <span class="block w-full opacity-70">{{ $childLabel }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </x-daisy::ui.navigation.menu>
                    </details>
                </li>
            @else
                <li class="w-full">
                    @if($href)
                        <a href="{{ $href }}" class="block w-full {{ $nodeIsActive ? 'menu-active font-semibold' : '' }}">{{ $label }}</a>
                    @else
                        <span class="block w-full opacity-70">{{ $label }}</span>
                    @endif
                </li>
            @endif
        @endforeach
    </x-daisy::ui.navigation.menu>
</nav>

