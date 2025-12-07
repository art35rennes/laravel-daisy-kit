@props([
    'title' => __('daisy::form.wizard.title'),
    'action' => '#',
    'method' => 'POST',
    'steps' => [], // [['key' => 'profile', 'label' => 'Profil', 'icon' => 'user']]
    'currentStep' => 1,
    'linear' => true,
    'allowClickNav' => false,
    'showSummary' => true,
    'prevText' => __('daisy::form.previous'),
    'nextText' => __('daisy::form.next'),
    'finishText' => __('daisy::form.finish'),
    'resumeSlot' => 'summary',
    'autoRefreshCsrf' => true,
    'wizardKey' => 'wizard',
])

@php
    use Art35rennes\DaisyKit\Helpers\WizardPersistence;
    
    // Générer un ID unique pour cette instance si non fourni
    $instanceId = $attributes->get('id') ?? 'wizard-'.uniqid();
    
    // Récupérer l'étape courante depuis la session ou utiliser celle fournie
    $current = WizardPersistence::getCurrentStep($wizardKey) ?? $currentStep;
    $totalSteps = count($steps);
    $isLastStep = $current >= $totalSteps;
    $isFirstStep = $current <= 1;
    
    // Récupérer les données persistées
    $wizardData = WizardPersistence::get($wizardKey);
    
    // Construire les items pour le stepper
    $stepItems = [];
    $stepsContents = [];

    foreach ($steps as $index => $step) {
        $stepKey = is_array($step) ? ($step['key'] ?? null) : null;
        $stepLabel = is_array($step) ? ($step['label'] ?? "Step ".($index + 1)) : (string) $step;
        $stepIcon = is_array($step) ? ($step['icon'] ?? null) : null;
        $stepIndex = $index + 1;
        
        $stepItems[] = [
            'key' => $stepKey ?? 'step_'.$stepIndex,
            'label' => $stepLabel,
            'icon' => $stepIcon,
            'disabled' => $linear && $stepIndex > $current,
        ];

        // Mapper le contenu des vues de démonstration sur l'index de l'étape.
        if ($stepKey && isset(${'step_'.$stepKey})) {
            $stepsContents[$stepIndex] = ${'step_'.$stepKey};
        }
    }
@endphp

<form 
    id="{{ $instanceId }}"
    action="{{ $action }}" 
    method="{{ strtoupper($method) }}" 
    data-module="wizard"
    data-wizard-key="{{ $wizardKey }}"
    data-wizard-instance-id="{{ $instanceId }}"
    data-linear="{{ $linear ? 'true' : 'false' }}"
    data-current-step="{{ $current }}"
    class="space-y-6"
    {{ $attributes->except(['id']) }}
>
    @if(strtoupper($method) !== 'GET')
        @csrf
    @endif
    
    @if(strtoupper($method) !== 'GET' && strtoupper($method) !== 'POST')
        @method($method)
    @endif
    
    @if($autoRefreshCsrf && strtoupper($method) !== 'GET')
        <x-daisy::ui.utilities.csrf-keeper />
    @endif
    
    <input type="hidden" name="_wizard_step" value="{{ $current }}" />
    <input type="hidden" name="_wizard_key" value="{{ $wizardKey }}" />
    
    @if($title)
        <h2 class="text-2xl font-semibold mb-6">{{ $title }}</h2>
    @endif
    
    {{-- Stepper --}}
    <x-daisy::ui.navigation.stepper 
        :items="$stepItems"
        :current="$current"
        :linear="$linear"
        :allowClickNav="$allowClickNav"
        :stepsContents="$stepsContents"
        :showControls="false"
    />
    
    {{-- Résumé final (dernière étape) --}}
    @if($isLastStep && $showSummary)
        <div class="card card-border bg-base-200" data-summary="summary" aria-label="summary">
            <div class="card-body">
                <h3 class="card-title">{{ __('daisy::form.wizard.summary') }}</h3>
                @isset($summary)
                    {!! $summary !!}
                @else
                    <p class="text-sm text-base-content/70">{{ __('daisy::form.wizard.summary_empty') }}</p>
                @endisset
            </div>
        </div>
    @endif
    
    {{-- Actions de navigation --}}
    <div class="flex items-center justify-between pt-6 border-t">
        <div>
            @if(!$isFirstStep)
                <button 
                    type="button" 
                    class="btn btn-ghost"
                    data-wizard-prev
                >
                    {{ $prevText }}
                </button>
            @endif
        </div>
        
        <div class="flex items-center gap-3">
            @isset($actions)
                {!! $actions !!}
            @endisset
            
            @if(!$isLastStep)
                <button 
                    type="button" 
                    class="btn btn-primary"
                    data-wizard-next
                >
                    {{ $nextText }}
                </button>
            @else
                <x-daisy::ui.inputs.button type="submit" variant="solid">
                    {{ $finishText }}
                </x-daisy::ui.inputs.button>
            @endif
        </div>
    </div>
</form>


