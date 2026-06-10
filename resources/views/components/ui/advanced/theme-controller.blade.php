@props([
    'themes' => null,
    'value' => null,
    'name' => 'theme',
    // Variant d'affichage: buttons (join) | dropdown
    'variant' => 'buttons',
    // Taille des boutons: sm | md | lg
    'size' => 'sm',
    // Style ghost sur les items
    'ghost' => true,
    // Texte du déclencheur dropdown
    'label' => 'Theme',
])

@php
    $themes = $themes ?? \Art35rennes\DaisyKit\Helpers\ThemeHelper::getAllThemes();
    $value = $value ?? \Art35rennes\DaisyKit\Helpers\ThemeHelper::getDefaultTheme();

    $sizeMap = [
        'sm' => 'btn-sm',
        'md' => 'btn-md',
        'lg' => 'btn-lg',
    ];
    $btnSize = $sizeMap[$size] ?? 'btn-sm';
    $itemBase = 'btn theme-controller ' . $btnSize;
    if ($ghost) {
        $itemBase .= ' btn-ghost';
    }

    $controllerAttributes = ['data-module' => 'theme-controller'];

    if (is_string($value) && trim($value) !== '') {
        $controllerAttributes['data-default-theme'] = trim($value);
    }
@endphp

@if($variant === 'dropdown')
    <div {{ $attributes->merge(array_merge(['class' => 'dropdown'], $controllerAttributes)) }}>
        <div tabindex="0" role="button" class="btn m-1">
            {{ $label }}
            <svg width="12" height="12" class="inline-block h-2 w-2 fill-current opacity-60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2048 2048"><path d="M1799 349l242 241-1017 1017L7 590l242-241 775 775 775-775z"></path></svg>
        </div>
        <ul tabindex="0" class="dropdown-content bg-base-300 rounded-box z-1 w-52 p-2 shadow">
            @foreach($themes as $t)
                <li>
                    <input type="radio" name="{{ $name }}" value="{{ $t }}" class="w-full {{ $itemBase }} btn-block justify-start" aria-label="{{ ucfirst($t) }}" @checked($value === $t) />
                </li>
            @endforeach
        </ul>
    </div>
@else
    <div {{ $attributes->merge(array_merge(['class' => 'join'], $controllerAttributes)) }}>
        @foreach($themes as $t)
            <input type="radio" name="{{ $name }}" value="{{ $t }}" class="join-item {{ $itemBase }}" aria-label="{{ ucfirst($t) }}" @checked($value === $t) />
        @endforeach
    </div>
@endif
