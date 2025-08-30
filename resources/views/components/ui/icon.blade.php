@props([
    'name' => null,
    'size' => null,
    'prefix' => null,
])

@php
    // Utiliser le préfixe fourni ou celui par défaut depuis la config
    $iconPrefix = $prefix ?? config('daisy-kit.icon_prefix', 'bi');
    
    // Logique améliorée pour déterminer si on doit ajouter le préfixe
    if ($name && !str_starts_with($name, $iconPrefix . '-')) {
        // Si le nom ne commence pas par le préfixe, l'ajouter
        $iconName = $iconPrefix . '-' . $name;
    } else {
        // Le nom contient déjà le préfixe complet
        $iconName = $name;
    }
    
    // Ajouter les classes de taille si spécifiées
    $sizeClass = '';
    if ($size) {
        $sizeClass = match($size) {
            'xs' => 'w-3 h-3',
            'sm' => 'w-4 h-4', 
            'md' => 'w-5 h-5',
            'lg' => 'w-6 h-6',
            'xl' => 'w-8 h-8',
            '2xl' => 'w-10 h-10',
            default => $size, // Taille personnalisée
        };
    }
@endphp

@if($iconName)
    <x-dynamic-component 
        :component="$iconName" 
        {{ $attributes->merge(['class' => $sizeClass]) }} 
    />
@endif
