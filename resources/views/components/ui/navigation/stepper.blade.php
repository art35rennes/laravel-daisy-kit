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
    
    // Fonction pour calculer les index ajustés pour les étapes désactivées
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
    
    // Fonction pour traiter un item et créer les données de l'étape
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
        
        // Gestion des couleurs
        if ($invalid) {
            $stepData['color'] = 'error';
        } elseif (!$disabled && $adjustedIndex <= $current) {
            $stepData['color'] = 'primary';
        }
        
        return $stepData;
    };
    
    // Traitement des items
    $disabledCounts = $calculateAdjustedIndexes($items);
    $stepsItems = [];
    
    foreach ($items as $idx => $item) {
        $stepsItems[] = $processStepItem($item, $idx, $disabledCounts, $current);
    }
    
    // Attributs du conteneur
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

    {{-- Contenus des étapes --}}
    <div class="space-y-4 relative z-0" data-stepper-contents>
        @foreach($stepsItems as $stepItem)
            @php $stepIndex = $stepItem['index']; @endphp
            
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
                    $hasExternalContent = is_array($stepsContents) && array_key_exists($stepIndex, $stepsContents);
                @endphp
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