@props([
    // Texte à copier (si fourni, sera utilisé au lieu du contenu du slot)
    'value' => null,
    // Texte à afficher (si fourni, remplace le slot pour l'affichage)
    // Permet de distinguer le texte affiché de la valeur copiée (comme option value vs texte)
    'display' => null,
    // Afficher une ligne pointillée en dessous
    'underline' => true,
    // Copier le HTML au lieu du texte brut (pour éléments complexes)
    'copyHtml' => false,
    // Position de l'icône: 'right' (par défaut) | 'left' | 'inline'
    'iconPosition' => 'right',
    // Taille de l'icône: xs|sm|md|lg|xl
    'iconSize' => 'sm',
    // Message de succès personnalisé
    'successMessage' => null,
    // Message d'erreur personnalisé
    'errorMessage' => null,
    // Tag HTML à utiliser (par défaut: span)
    'tag' => 'span',
])

@php
    $containerClasses = 'copyable';
    
    if ($underline) {
        $containerClasses .= ' copyable-underline';
    }
    
    // Extraire les classes personnalisées
    $customClasses = $attributes->get('class');
    $attributes = $attributes->except('class');
    
    // Construire les attributs data-* pour le greffon JS
    $dataAttributes = [];
    if ($value !== null) {
        $dataAttributes['data-copy-value'] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    if ($copyHtml) {
        $dataAttributes['data-copy-html'] = 'true';
    }
    if ($iconSize !== 'sm') {
        $dataAttributes['data-icon-size'] = $iconSize;
    }
    if ($iconPosition !== 'right') {
        $dataAttributes['data-icon-position'] = $iconPosition;
    }
    if ($successMessage !== null) {
        $dataAttributes['data-success-message'] = htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8');
    }
    if ($errorMessage !== null) {
        $dataAttributes['data-error-message'] = htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8');
    }
@endphp

<{{ $tag }} 
    {{ $attributes->merge(array_merge([
        'class' => trim($containerClasses . ' ' . ($customClasses ?? '')),
    ], $dataAttributes)) }}
>
    {{ $display ?? $slot }}
</{{ $tag }}>
