<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
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
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
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
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<<?php echo e($tag); ?> 
    <?php echo e($attributes->merge(array_merge([
        'class' => trim($containerClasses . ' ' . ($customClasses ?? '')),
    ], $dataAttributes))); ?>

>
    <?php echo e($display ?? $slot); ?>

</<?php echo e($tag); ?>>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/utilities/copyable.blade.php ENDPATH**/ ?>