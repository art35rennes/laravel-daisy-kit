@props([
    'items' => [],
    'current' => 1,
    'stepsContents' => [],
    'vertical' => false,
    'horizontal' => false,
    'horizontalAt' => null,
    'linear' => false,
    'allowClickNav' => true,
    'persist' => false,
    'showControls' => true,
    'prevText' => 'Précédent',
    'nextText' => 'Suivant',
    'finishText' => 'Terminer',
    'controlsClass' => '',
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
])

@php
    $rootId = $attributes->get('id');
    
    // Calcul des index ajustés : les étapes désactivées ne sont pas comptées dans la numérotation.
    // Exemple : [step1, step2(disabled), step3] → step1=1, step2=disabled, step3=2 (pas 3).
    $calculateAdjustedIndexes = function($items) {
        $disabledCounts = [];
        $disabledCount = 0;
        
        foreach ($items as $idx => $item) {
            $disabled = is_array($item) && ($item['disabled'] ?? false);
            $disabledCounts[$idx] = $disabledCount;
            if ($disabled) $disabledCount++;
        }
        
        return $disabledCounts;
    };
    
    // Traitement d'un item d'étape : extraction des données (label, icon, état) et calcul de l'index ajusté.
    $processStepItem = function($item, $idx, $disabledCounts, $current) {
        $adjustedIndex = $idx + 1 - $disabledCounts[$idx];
        $disabled = is_array($item) && ($item['disabled'] ?? false);
        $invalid = is_array($item) && ($item['invalid'] ?? false);
        
        $stepData = [
            'label' => is_array($item) ? ($item['label'] ?? "Step {$adjustedIndex}") : (string) $item,
            'icon' => is_array($item) ? ($item['icon'] ?? null) : null,
            'disabled' => $disabled,
            'invalid' => $invalid,
            'index' => $adjustedIndex,
        ];
        
        // Détermination de la couleur selon l'état : error si invalide, primary si complétée/active.
        if ($invalid) {
            $stepData['color'] = 'error';
        } elseif (!$disabled && $adjustedIndex <= $current) {
            $stepData['color'] = 'primary';
        }
        
        return $stepData;
    };
    
    // Traitement de tous les items pour générer la liste des étapes avec index ajustés.
    $disabledCounts = $calculateAdjustedIndexes($items);
    $stepsItems = [];
    
    foreach ($items as $idx => $item) {
        $stepsItems[] = $processStepItem($item, $idx, $disabledCounts, $current);
    }
    
    // Préparation des attributs du conteneur pour l'initialisation JavaScript.
    $containerAttrs = $attributes->class('w-full relative isolate')->merge([
        'data-module' => ($module ?? 'stepper'),
        'data-stepper' => true,
        'data-linear' => $linear ? 'true' : 'false',
        'data-allow-click' => $allowClickNav ? 'true' : 'false',
        'data-persist' => $persist ? 'true' : 'false',
        'data-current' => (int) $current,
    ]);
@endphp

<div {{ $containerAttrs }}>
    {{-- En-têtes des étapes --}}
    <div class="mb-4" data-stepper-headers>
        <x-daisy::ui.navigation.steps 
            :items="$stepsItems" 
            :current="$current"
            :vertical="$vertical"
            :horizontal="$horizontal"
            :horizontalAt="$horizontalAt"
            :allowClickNav="$allowClickNav"
            :rootId="$rootId"
        />
    </div>

    {{-- Contenus des étapes : un seul panneau visible à la fois (celui correspondant à $current) --}}
    <div class="space-y-4 relative z-0" data-stepper-contents>
        @foreach($stepsItems as $stepItem)
            @php $stepIndex = $stepItem['index']; @endphp
            
            {{-- Panneau d'étape : masqué par défaut sauf si c'est l'étape courante --}}
            <div class="@if($stepIndex !== (int)$current) hidden @endif" 
                 data-step-content 
                 data-step-index="{{ $stepIndex }}"
                 @if($rootId) 
                     id="{{ $rootId }}-panel-{{ $stepIndex }}"
                     aria-labelledby="{{ $rootId }}-header-{{ $stepIndex }}"
                 @endif
                 role="region"
                 aria-hidden="{{ $stepIndex !== (int)$current ? 'true' : 'false' }}">
                @php
                    // Vérification de la présence de contenu externe (via $stepsContents array).
                    $hasExternalContent = is_array($stepsContents) && array_key_exists($stepIndex, $stepsContents);
                @endphp
                {{-- Priorité d'affichage : contenu externe > slot nommé (step_X) > slot par défaut --}}
                @if ($hasExternalContent)
                    {!! $stepsContents[$stepIndex] instanceof \Illuminate\View\ComponentSlot ? $stepsContents[$stepIndex]->toHtml() : (string) $stepsContents[$stepIndex] !!}
                @elseif (isset(${'step_'.$stepIndex}))
                    {{ ${'step_'.$stepIndex} }}
                @elseif ($slot->isNotEmpty())
                    {{ $slot }}
                @endif
            </div>
        @endforeach
    </div>

    {{-- Contrôles de navigation --}}
    @if($showControls)
        <div class="mt-4 flex items-center justify-between {{ $controlsClass }}" data-stepper-controls>
            <x-daisy::ui.inputs.button variant="ghost" size="sm" data-stepper-prev>
                {{ $prevText }}
            </x-daisy::ui.inputs.button>
            
            <div class="flex gap-2">
                <x-daisy::ui.inputs.button size="sm" data-stepper-next>
                    {{ $nextText }}
                </x-daisy::ui.inputs.button>
                
                <x-daisy::ui.inputs.button size="sm" color="success" data-stepper-finish class="hidden">
                    {{ $finishText }}
                </x-daisy::ui.inputs.button>
            </div>
        </div>
    @endif
</div>

@include('daisy::components.partials.assets')