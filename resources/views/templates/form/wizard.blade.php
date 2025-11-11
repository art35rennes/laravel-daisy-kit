@props([
    'title' => __('form.wizard'),
    'theme' => null,
    'action' => '#',
    'method' => 'POST',
    'steps' => [],
    'currentStep' => 1,
    'linear' => true,
    'allowClickNav' => false,
    'showControls' => true,
    'prevText' => __('form.previous'),
    'nextText' => __('form.next'),
    'finishText' => __('form.finish'),
    'validateOnStep' => true,
    'validateOnSubmit' => true,
    // Orientation du stepper (vertical = liste verticale, headers au-dessus par défaut)
    'vertical' => false,
    // Point de rupture pour passer en horizontal (ex: 'md')
    'horizontalAt' => null,
    // Mode de fonctionnement : 'accumulation' (post uniquement à la fin) ou 'workflow' (post à chaque étape)
    'mode' => 'accumulation',
])

@php
    // Récupérer l'étape courante depuis la session ou utiliser la prop
    $step = session('wizard_step', $currentStep);
    
    // Préparer les items pour le stepper
    $stepperItems = [];
    foreach ($steps as $index => $stepConfig) {
        $stepperItems[] = [
            'label' => $stepConfig['label'] ?? "Étape " . ($index + 1),
            'icon' => $stepConfig['icon'] ?? null,
            'disabled' => $stepConfig['disabled'] ?? false,
            'invalid' => $stepConfig['invalid'] ?? false,
        ];
    }
    
    // Générer un ID unique pour le stepper
    $stepperId = 'wizard-' . uniqid();
    
    // Récupérer les données de la session pour pré-remplir
    $wizardData = session('wizard_data', []);
@endphp

<x-daisy::layout.app :title="$title" :theme="$theme" :container="true">
    <div class="space-y-6">
        @if($title)
            <div class="text-center">
                <h1 class="text-2xl font-semibold">{{ $title }}</h1>
            </div>
        @endif

        {{-- Messages d'erreur globaux --}}
        @if($errors->any())
            <x-daisy::ui.feedback.alert color="error">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-daisy::ui.feedback.alert>
        @endif

        <form action="{{ $action }}" method="{{ $method }}" id="wizard-form" class="space-y-6">
            @csrf
            @if(in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
                @method($method)
            @endif

            {{-- Champ caché pour l'étape courante --}}
            <input type="hidden" name="step" value="{{ $step }}" id="wizard-step-input">
            <input type="hidden" name="wizard_action" value="next" id="wizard-action-input">

            {{-- Stepper --}}
            <x-daisy::ui.navigation.stepper
                :id="$stepperId"
                :items="$stepperItems"
                :current="$step"
                :vertical="$vertical"
                :horizontalAt="$horizontalAt"
                :stepsContents="(function() use ($steps) {
                    $contents = [];
                    foreach ($steps as $index => $stepConfig) {
                        $stepNumber = $index + 1;
                        $stepId = $stepConfig['id'] ?? ('step_' . $stepNumber);
                        $slotNameStep = 'step_' . $stepNumber;
                        $slotNameId = 'step_' . $stepId;
                        
                        if (isset($$slotNameStep) && $$slotNameStep !== null) {
                            $contents[$stepNumber] = $$slotNameStep instanceof \Illuminate\View\ComponentSlot
                                ? $$slotNameStep->toHtml()
                                : (string) $$slotNameStep;
                        } elseif (isset($$slotNameId) && $$slotNameId !== null) {
                            $contents[$stepNumber] = $$slotNameId instanceof \Illuminate\View\ComponentSlot
                                ? $$slotNameId->toHtml()
                                : (string) $$slotNameId;
                        }
                    }
                    return $contents;
                })()"
                :linear="$linear"
                :allowClickNav="$allowClickNav"
                :showControls="$showControls"
                :prevText="$prevText"
                :nextText="$nextText"
                :finishText="$finishText"
            >
            </x-daisy::ui.navigation.stepper>
        </form>
    </div>
</x-daisy::layout.app>

@push('scripts')
<script>
    (function() {
        const form = document.getElementById('wizard-form');
        const stepInput = document.getElementById('wizard-step-input');
        const actionInput = document.getElementById('wizard-action-input');
        const stepperRoot = document.getElementById('{{ $stepperId }}');
        
        if (!form || !stepInput || !actionInput || !stepperRoot) return;

        // Écouter les événements du stepper
        stepperRoot.addEventListener('stepper:change', function(e) {
            const newStep = e.detail.current;
            stepInput.value = newStep;
        });

        // Intercepter les boutons du stepper pour soumettre le formulaire
        const prevBtn = stepperRoot.querySelector('[data-stepper-prev]');
        const nextBtn = stepperRoot.querySelector('[data-stepper-next]');
        const finishBtn = stepperRoot.querySelector('[data-stepper-finish]');

        if (prevBtn) {
            prevBtn.addEventListener('click', function(e) {
                e.preventDefault();
                actionInput.value = 'prev';
                
                @if($validateOnStep)
                    // En mode linéaire, on peut revenir en arrière sans validation
                    form.submit();
                @else
                    form.submit();
                @endif
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                actionInput.value = 'next';
                
                @if($validateOnStep)
                    // Valider l'étape actuelle avant de continuer
                    const currentStep = parseInt(stepInput.value, 10);
                    const isValid = validateStep(currentStep);
                    
                    if (!isValid) {
                        // Afficher un message d'erreur
                        alert('{{ __('form.complete_step_first') }}');
                        return;
                    }
                @endif
                
                form.submit();
            });
        }

        if (finishBtn) {
            finishBtn.addEventListener('click', function(e) {
                e.preventDefault();
                actionInput.value = 'finish';
                
                @if($validateOnSubmit)
                    // Valider tout le formulaire avant de soumettre
                    const isValid = validateAllSteps();
                    
                    if (!isValid) {
                        alert('{{ __('form.complete_step_first') }}');
                        return;
                    }
                @endif
                
                form.submit();
            });
        }

        // Fonction de validation d'une étape
        function validateStep(step) {
            const stepContent = stepperRoot.querySelector(`[data-step-content][data-step-index="${step}"]`);
            if (!stepContent) return true;

            const inputs = stepContent.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('input-error', 'select-error', 'textarea-error');
                } else {
                    input.classList.remove('input-error', 'select-error', 'textarea-error');
                }
            });

            return isValid;
        }

        // Fonction de validation de toutes les étapes
        function validateAllSteps() {
            const totalSteps = {{ count($steps) }};
            let allValid = true;

            for (let i = 1; i <= totalSteps; i++) {
                if (!validateStep(i)) {
                    allValid = false;
                }
            }

            return allValid;
        }
    })();
</script>
@endpush

