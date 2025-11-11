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

            {{-- Champs cachés pour l'étape courante et le mode --}}
            <input type="hidden" name="step" value="{{ $step }}" id="wizard-step-input">
            <input type="hidden" name="wizard_action" value="next" id="wizard-action-input">
            <input type="hidden" name="wizard_mode" value="{{ $mode }}" id="wizard-mode-input">

            {{-- Stepper --}}
            <x-daisy::ui.navigation.stepper
                :id="$stepperId"
                :items="$stepperItems"
                :current="$step"
                :vertical="$vertical"
                :horizontalAt="$horizontalAt"
                :linear="$linear"
                :allowClickNav="$allowClickNav"
                :showControls="$showControls"
                :prevText="$prevText"
                :nextText="$nextText"
                :finishText="$finishText"
            >
                @foreach($steps as $index => $stepConfig)
                    @php
                        $stepNumber = $index + 1;
                        $stepId = $stepConfig['id'] ?? ('step_' . $stepNumber);
                        $slotNameStep = 'step_' . $stepNumber;
                        $slotNameId = 'step_' . $stepId;
                    @endphp
                    @if(isset($$slotNameStep) && $$slotNameStep !== null)
                        <x-slot name="step_{{ $stepNumber }}">
                            {{ $$slotNameStep }}
                        </x-slot>
                    @elseif(isset($$slotNameId) && $$slotNameId !== null)
                        <x-slot name="step_{{ $stepNumber }}">
                            {{ $$slotNameId }}
                        </x-slot>
                    @endif
                @endforeach
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
        const modeInput = document.getElementById('wizard-mode-input');
        const stepperRoot = document.getElementById('{{ $stepperId }}');
        
        if (!form || !stepInput || !actionInput || !modeInput || !stepperRoot) return;

        const mode = modeInput.value || 'accumulation';
        const totalSteps = {{ count($steps) }};
        
        // Stockage local pour le mode accumulation
        const storedData = new Map();

        // Écouter les événements du stepper
        stepperRoot.addEventListener('stepper:change', function(e) {
            const newStep = e.detail.current;
            stepInput.value = newStep;
        });

        // Fonction pour sauvegarder les données de l'étape actuelle (mode accumulation)
        function saveCurrentStepData() {
            if (mode !== 'accumulation') return;
            
            const currentStep = parseInt(stepInput.value, 10);
            const stepContent = stepperRoot.querySelector(`[data-step-content][data-step-index="${currentStep}"]`);
            if (!stepContent) return;

            const formData = new FormData(form);
            const stepData = {};
            
            // Récupérer tous les champs de l'étape actuelle
            stepContent.querySelectorAll('input, select, textarea').forEach(field => {
                if (field.name && field.name !== 'step' && field.name !== 'wizard_action' && field.name !== 'wizard_mode' && field.name !== '_token' && field.name !== '_method') {
                    if (field.type === 'checkbox') {
                        stepData[field.name] = field.checked;
                    } else if (field.type === 'radio') {
                        if (field.checked) {
                            stepData[field.name] = field.value;
                        }
                    } else {
                        stepData[field.name] = field.value;
                    }
                }
            });
            
            storedData.set(currentStep, stepData);
        }

        // Fonction pour restaurer les données d'une étape (mode accumulation)
        function restoreStepData(step) {
            if (mode !== 'accumulation') return;
            
            const stepData = storedData.get(step);
            if (!stepData) return;

            const stepContent = stepperRoot.querySelector(`[data-step-content][data-step-index="${step}"]`);
            if (!stepContent) return;

            Object.keys(stepData).forEach(name => {
                const field = stepContent.querySelector(`[name="${name}"]`);
                if (!field) return;

                if (field.type === 'checkbox') {
                    field.checked = stepData[name];
                } else if (field.type === 'radio') {
                    if (field.value === stepData[name]) {
                        field.checked = true;
                    }
                } else {
                    field.value = stepData[name];
                }
            });
        }

        // Intercepter les boutons du stepper
        const prevBtn = stepperRoot.querySelector('[data-stepper-prev]');
        const nextBtn = stepperRoot.querySelector('[data-stepper-next]');
        const finishBtn = stepperRoot.querySelector('[data-stepper-finish]');

        if (prevBtn) {
            prevBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (mode === 'accumulation') {
                    // En mode accumulation, on change juste d'étape sans soumettre
                    const currentStep = parseInt(stepInput.value, 10);
                    if (currentStep > 1) {
                        saveCurrentStepData();
                        const newStep = currentStep - 1;
                        stepInput.value = newStep;
                        restoreStepData(newStep);
                        
                        // Déclencher l'événement pour mettre à jour l'UI
                        stepperRoot.dispatchEvent(new CustomEvent('stepper:change', {
                            detail: { current: newStep }
                        }));
                    }
                } else {
                    // En mode workflow, on soumet pour récupérer les données enrichies
                    actionInput.value = 'prev';
                    form.submit();
                }
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const currentStep = parseInt(stepInput.value, 10);
                
                @if($validateOnStep)
                    // Valider l'étape actuelle avant de continuer
                    const isValid = validateStep(currentStep);
                    
                    if (!isValid) {
                        alert('{{ __('form.complete_step_first') }}');
                        return;
                    }
                @endif
                
                if (mode === 'accumulation') {
                    // En mode accumulation, on sauvegarde et on change d'étape sans soumettre
                    saveCurrentStepData();
                    if (currentStep < totalSteps) {
                        const newStep = currentStep + 1;
                        stepInput.value = newStep;
                        restoreStepData(newStep);
                        
                        // Déclencher l'événement pour mettre à jour l'UI
                        stepperRoot.dispatchEvent(new CustomEvent('stepper:change', {
                            detail: { current: newStep }
                        }));
                    }
                } else {
                    // En mode workflow, on soumet pour traiter l'étape et enrichir la suivante
                    actionInput.value = 'next';
                    form.submit();
                }
            });
        }

        if (finishBtn) {
            finishBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                @if($validateOnSubmit)
                    // Valider tout le formulaire avant de soumettre
                    const isValid = validateAllSteps();
                    
                    if (!isValid) {
                        alert('{{ __('form.complete_step_first') }}');
                        return;
                    }
                @endif
                
                if (mode === 'accumulation') {
                    // En mode accumulation, on sauvegarde toutes les données et on soumet
                    saveCurrentStepData();
                    
                    // Injecter toutes les données sauvegardées dans le formulaire
                    storedData.forEach((stepData, step) => {
                        Object.keys(stepData).forEach(name => {
                            const existingInput = form.querySelector(`[name="${name}"]`);
                            if (!existingInput) {
                                // Créer un input caché pour les données des autres étapes
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = name;
                                hiddenInput.value = stepData[name];
                                form.appendChild(hiddenInput);
                            }
                        });
                    });
                }
                
                actionInput.value = 'finish';
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

