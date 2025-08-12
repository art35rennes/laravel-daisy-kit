@props([
    // items: [ ['label' => 'Step 1', 'icon' => null, 'disabled' => false, 'invalid' => false], ... ]
    'items' => [],
    // index 1-based courant
    'current' => 1,
    // Orientation
    'vertical' => false,
    'horizontal' => false,
    'horizontalAt' => null, // ex: 'lg'
    // Navigation
    'linear' => false,           // navigation séquentielle
    'allowClickNav' => true,     // clic sur step header autorisé
    // Persistance sessionStorage
    'persist' => false,
    // Contrôles intégrés
    'showControls' => true,
    'prevText' => 'Précédent',
    'nextText' => 'Suivant',
    'finishText' => 'Terminer',
    'controlsClass' => '',
])

@php
    $containerAttrs = $attributes->class('w-full')->merge([
        'data-stepper' => true,
        'data-linear' => $linear ? 'true' : 'false',
        'data-allow-click' => $allowClickNav ? 'true' : 'false',
        'data-persist' => $persist ? 'true' : 'false',
        'data-current' => (int) $current,
    ]);

    $stepsClasses = 'steps';
    if ($horizontalAt) {
        $stepsClasses .= ' steps-vertical '.($horizontalAt).':steps-horizontal';
    } elseif ($vertical) {
        $stepsClasses .= ' steps-vertical';
    } elseif ($horizontal) {
        $stepsClasses .= ' steps-horizontal';
    }
@endphp

<div {{ $containerAttrs }}>
    <ul class="{{ $stepsClasses }} mb-4" data-stepper-headers>
        @foreach($items as $idx => $item)
            @php
                $i = $idx + 1;
                $label = is_array($item) ? ($item['label'] ?? 'Step '.$i) : (string) $item;
                $icon = is_array($item) ? ($item['icon'] ?? null) : null;
                $disabled = (bool) (is_array($item) ? ($item['disabled'] ?? false) : false);
                $invalid = (bool) (is_array($item) ? ($item['invalid'] ?? false) : false);
                $classes = 'step';
                if ($i <= $current) $classes .= ' step-primary';
                if ($invalid) $classes .= ' step-error';
                if ($disabled) $classes .= ' pointer-events-none opacity-50';
            @endphp
            <li class="{{ $classes }}" data-step-index="{{ $i }}">
                @if(!is_null($icon))
                    <span class="step-icon">{!! $icon !!}</span>
                @endif
                {{ $label }}
            </li>
        @endforeach
    </ul>

    <div class="space-y-4" data-stepper-contents>
        @foreach($items as $idx => $item)
            @php $i = $idx + 1; @endphp
            <div class="@if($i !== (int)$current) hidden @endif" data-step-content data-step-index="{{ $i }}">
                @if (isset(${'step_'.$i}))
                    {{ ${'step_'.$i} }}
                @else
                    {{ $slot }}
                @endif
            </div>
        @endforeach
    </div>

    @if($showControls)
        <div class="mt-4 flex items-center justify-between {{ $controlsClass }}" data-stepper-controls>
            <x-daisy::ui.button variant="ghost" size="sm" data-stepper-prev>{{ $prevText }}</x-daisy::ui.button>
            <div class="flex gap-2">
                <x-daisy::ui.button size="sm" data-stepper-next>{{ $nextText }}</x-daisy::ui.button>
                <x-daisy::ui.button size="sm" color="success" data-stepper-finish class="hidden">{{ $finishText }}</x-daisy::ui.button>
            </div>
        </div>
    @endif
</div>


