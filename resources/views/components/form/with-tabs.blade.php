@props([
    'title' => __('form.form'),
    'theme' => null,
    'action' => '#',
    'method' => 'POST',
    'tabs' => [],
    'activeTab' => null,
    'tabsStyle' => 'box',
    'tabsPlacement' => 'top',
    'validateAllTabs' => false,
    'highlightErrors' => true,
    'validateOnNavigation' => true,
])

@php
    // Déterminer l'onglet actif
    $currentActiveTab = $activeTab ?? old('_active_tab', $tabs[0]['id'] ?? null);
    
    // Compter les erreurs par onglet si highlightErrors est activé
    $tabErrors = [];
    $tabWarnings = [];
    
    if ($highlightErrors) {
        foreach ($tabs as $tab) {
            $tabId = $tab['id'] ?? null;
            if (!$tabId) {
                continue;
            }
            
            // Compter les erreurs de validation serveur
            $errorCount = 0;
            $tabFields = $tab['fields'] ?? [];
            
            if (!empty($tabFields)) {
                foreach ($tabFields as $field) {
                    if ($errors->has($field)) {
                        $errorCount++;
                    }
                }
            } else {
                // Par défaut, on cherche les erreurs qui commencent par le préfixe de l'onglet
                $prefix = $tabId . '_';
                foreach ($errors->keys() as $key) {
                    if (str_starts_with($key, $prefix)) {
                        $errorCount++;
                    }
                }
            }
            
            if ($errorCount > 0) {
                $tabErrors[$tabId] = $errorCount;
            }
        }
    }
    
    // Calculer le nombre total de tabs
    $totalTabs = count($tabs);
    $currentTabIndex = 0;
    foreach ($tabs as $index => $tab) {
        if (($tab['id'] ?? null) === $currentActiveTab) {
            $currentTabIndex = $index;
            break;
        }
    }
    $isFirstTab = ($currentTabIndex === 0);
    $isLastTab = ($currentTabIndex === $totalTabs - 1);
    
    // Préparer les items pour le composant tabs
    $tabsItems = [];
    foreach ($tabs as $tab) {
        $tabId = $tab['id'] ?? null;
        $label = $tab['label'] ?? 'Tab';
        $icon = $tab['icon'] ?? null;
        $errorCount = $tabErrors[$tabId] ?? 0;
        $warningCount = $tabWarnings[$tabId] ?? 0;
        
        // Construire le label avec badge d'erreur ou warning si nécessaire
        $displayLabel = $label;
        if ($errorCount > 0) {
            $displayLabel = $label . ' <span class="badge badge-error badge-sm ml-2" data-error-badge="' . $tabId . '">' . $errorCount . '</span>';
        } elseif ($warningCount > 0) {
            $displayLabel = $label . ' <span class="badge badge-warning badge-sm ml-2" data-warning-badge="' . $tabId . '">' . $warningCount . '</span>';
        }
        
        $tabsItems[] = [
            'label' => $displayLabel,
            'icon' => $icon,
            'active' => ($tabId === $currentActiveTab),
            'disabled' => $tab['disabled'] ?? false,
        ];
    }
    
    // Mapping du style
    $variantMap = [
        'box' => 'box',
        'boxed' => 'box',
        'border' => 'border',
        'bordered' => 'border',
        'lift' => 'lifted',
        'lifted' => 'lifted',
    ];
    $tabsVariant = $variantMap[$tabsStyle] ?? 'box';
@endphp

<x-daisy::layout.app :title="$title" :theme="$theme" :container="true">
    <div class="space-y-6">
        @if($title)
            <div class="text-center">
                <h1 class="text-2xl font-semibold">{{ $title }}</h1>
            </div>
        @endif

        {{-- Messages d'erreur globaux --}}
        @if($errors->any() && $validateAllTabs)
            <x-daisy::ui.feedback.alert color="error">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-daisy::ui.feedback.alert>
        @endif

        <form action="{{ $action }}" method="{{ $method }}" class="space-y-6">
            @csrf
            @if(in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
                @method($method)
            @endif

            {{-- Champ caché pour persister l'onglet actif --}}
            <input type="hidden" name="_active_tab" value="{{ $currentActiveTab }}" id="active-tab-input">

            {{-- Onglets --}}
            <div role="tablist" class="tabs {{ $tabsVariant ? 'tabs-'.$tabsVariant : '' }} tabs-{{ $tabsPlacement }}">
                @foreach($tabsItems as $index => $tabItem)
                    @php
                        $tabId = $tabs[$index]['id'] ?? null;
                        $isActive = !empty($tabItem['active']);
                        $isDisabled = !empty($tabItem['disabled']);
                        $label = $tabItem['label'] ?? 'Tab';
                        $tabClasses = 'tab'.($isActive ? ' tab-active' : '').($isDisabled ? ' tab-disabled' : '');
                    @endphp
                    <button 
                        type="button"
                        role="tab" 
                        class="{{ $tabClasses }}" 
                        data-tab-id="{{ $tabId }}"
                        data-tab-label="{{ $tabs[$index]['label'] ?? 'Tab' }}"
                        aria-selected="{{ $isActive ? 'true' : 'false' }}"
                        @disabled($isDisabled)
                    >{!! $label !!}</button>
                @endforeach
            </div>

            {{-- Contenu des onglets --}}
            <div class="space-y-4">
                @foreach($tabs as $index => $tab)
                    @php
                        $tabId = $tab['id'] ?? null;
                        $isActive = ($tabId === $currentActiveTab);
                        $slotName = 'tab_' . $tabId;
                    @endphp
                    
                    @if($tabId && isset($$slotName))
                        <div class="@if(!$isActive) hidden @endif" data-tab-content data-tab-id="{{ $tabId }}">
                            {{ $$slotName }}
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Actions du formulaire --}}
            <div class="flex items-center justify-between gap-3">
                <div>
                    @if(!$isFirstTab)
                        <x-daisy::ui.inputs.button 
                            type="button" 
                            variant="ghost" 
                            id="btn-previous"
                            data-action="previous"
                        >
                            {{ __('form.previous') }}
                        </x-daisy::ui.inputs.button>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <x-daisy::ui.inputs.button type="button" variant="ghost" onclick="window.history.back()">
                        {{ __('form.cancel') }}
                    </x-daisy::ui.inputs.button>
                    @if($isLastTab)
                        <x-daisy::ui.inputs.button type="submit" id="btn-submit">
                            {{ __('form.validate') }}
                        </x-daisy::ui.inputs.button>
                    @else
                        <x-daisy::ui.inputs.button 
                            type="button" 
                            id="btn-next"
                            data-action="next"
                        >
                            {{ __('form.next') }}
                        </x-daisy::ui.inputs.button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</x-daisy::layout.app>

@push('scripts')
<script>
(function() {
    'use strict';
    
    function initFormWithTabs() {
        const activeTabInput = document.getElementById('active-tab-input');
        if (!activeTabInput) {
            console.warn('[Form with tabs] active-tab-input not found');
            return;
        }

        const form = activeTabInput.closest('form');
        const tabButtons = document.querySelectorAll('[role="tablist"] button[role="tab"][data-tab-id]');
        const tabContents = document.querySelectorAll('[data-tab-content]');
        const tabs = Array.from(tabButtons).map(btn => btn.dataset.tabId).filter(Boolean);
        
        if (tabs.length === 0) {
            console.warn('[Form with tabs] No tabs found');
            return;
        }
        
        console.log('[Form with tabs] Initializing...', { tabs, tabButtons: tabButtons.length });
        
        // Fonction pour valider un onglet
        function validateTab(tabId) {
            const tabContent = document.querySelector(`[data-tab-content][data-tab-id="${tabId}"]`);
            if (!tabContent) return { valid: true, errors: 0 };
            
            // Chercher les champs avec attribut required directement
            const directRequired = tabContent.querySelectorAll('input[required], select[required], textarea[required]');
            
            // Chercher les form-field avec required (détecté par la présence de l'astérisque dans le label)
            const formFields = tabContent.querySelectorAll('.form-control');
            const requiredFormFields = Array.from(formFields).filter(field => {
                const label = field.querySelector('label');
                if (!label) return false;
                // Chercher l'astérisque ou le span avec text-error (indicateur required)
                return label.querySelector('.text-error') !== null || label.textContent.includes('*');
            });
            
            // Collecter tous les champs requis
            const requiredFields = [...directRequired];
            requiredFormFields.forEach(formField => {
                const input = formField.querySelector('input, select, textarea');
                if (input && !input.hasAttribute('required')) {
                    requiredFields.push(input);
                }
            });
            
            let errorCount = 0;
            
            requiredFields.forEach(field => {
                const value = field.value?.trim() || '';
                const isEmpty = value === '';
                const isCheckbox = field.type === 'checkbox' && !field.checked;
                const isRadio = field.type === 'radio' && !field.checked;
                
                if (isEmpty || isCheckbox || isRadio) {
                    errorCount++;
                    field.classList.add('input-error', 'select-error', 'textarea-error');
                } else {
                    field.classList.remove('input-error', 'select-error', 'textarea-error');
                }
            });
            
            return { valid: errorCount === 0, errors: errorCount };
        }
        
        // Fonction pour mettre à jour les badges d'erreur/warning
        function updateTabBadges() {
            tabs.forEach(tabId => {
                const validation = validateTab(tabId);
                const errorBadge = document.querySelector(`[data-error-badge="${tabId}"]`);
                const warningBadge = document.querySelector(`[data-warning-badge="${tabId}"]`);
                const tabButton = document.querySelector(`[role="tab"][data-tab-id="${tabId}"]`);
                
                if (!tabButton) return;
                
                // Si un badge d'erreur serveur existe, on le garde (priorité)
                if (errorBadge) {
                    // Supprimer uniquement le badge warning si présent
                    if (warningBadge) warningBadge.remove();
                    return;
                }
                
                // Supprimer le badge warning existant
                if (warningBadge) warningBadge.remove();
                
                // Ajouter le badge warning uniquement s'il y a des erreurs de validation côté client
                if (validation.errors > 0) {
                    // Récupérer le label original depuis l'attribut data
                    const originalLabel = tabButton.dataset.tabLabel || '';
                    
                    // Préserver les éléments existants (icônes, etc.)
                    const existingElements = Array.from(tabButton.childNodes);
                    const iconElements = existingElements.filter(node => 
                        node.nodeType === 1 && (node.tagName === 'SVG' || node.tagName === 'I' || node.querySelector('svg, i'))
                    );
                    
                    // Supprimer uniquement les badges warning existants
                    const warningBadges = tabButton.querySelectorAll('[data-warning-badge]');
                    warningBadges.forEach(b => b.remove());
                    
                    // Créer le nouveau badge
                    const badge = document.createElement('span');
                    badge.className = 'badge badge-warning badge-sm ml-2';
                    badge.setAttribute('data-warning-badge', tabId);
                    badge.textContent = validation.errors;
                    
                    // Reconstruire le contenu en préservant les icônes
                    tabButton.innerHTML = '';
                    iconElements.forEach(el => tabButton.appendChild(el.cloneNode(true)));
                    if (originalLabel) {
                        tabButton.appendChild(document.createTextNode(' ' + originalLabel + ' '));
                    }
                    tabButton.appendChild(badge);
                } else {
                    // Nettoyer le label si plus d'erreurs (mais garder les badges d'erreur serveur)
                    const originalLabel = tabButton.dataset.tabLabel || '';
                    const existingElements = Array.from(tabButton.childNodes);
                    const iconElements = existingElements.filter(node => 
                        node.nodeType === 1 && (node.tagName === 'SVG' || node.tagName === 'I' || node.querySelector('svg, i'))
                    );
                    const errorBadges = tabButton.querySelectorAll('[data-error-badge]');
                    
                    // Supprimer uniquement les badges warning
                    const warningBadges = tabButton.querySelectorAll('[data-warning-badge]');
                    warningBadges.forEach(b => b.remove());
                    
                    // Reconstruire sans les badges warning
                    tabButton.innerHTML = '';
                    iconElements.forEach(el => tabButton.appendChild(el.cloneNode(true)));
                    if (originalLabel) {
                        tabButton.appendChild(document.createTextNode(' ' + originalLabel));
                    }
                    errorBadges.forEach(b => tabButton.appendChild(b));
                }
            });
        }
        
        // Fonction pour changer d'onglet
        function switchTab(tabId, button, skipValidation = false) {
            if (!tabId) return false;
            
            // Valider l'onglet actuel si nécessaire
            const currentTabId = activeTabInput.value;
            const shouldValidate = {{ $validateOnNavigation ? 'true' : 'false' }};
            if (!skipValidation && currentTabId && currentTabId !== tabId && shouldValidate) {
                const validation = validateTab(currentTabId);
                if (!validation.valid) {
                    updateTabBadges();
                    return false;
                }
            }
            
            // Mettre à jour le champ caché
            activeTabInput.value = tabId;
            
            // Afficher/masquer les contenus
            tabContents.forEach(content => {
                if (content.dataset.tabId === tabId) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
            });
            
            // Mettre à jour l'état actif des onglets
            tabButtons.forEach(btn => {
                btn.classList.remove('tab-active');
                btn.setAttribute('aria-selected', 'false');
            });
            
            if (button) {
                button.classList.add('tab-active');
                button.setAttribute('aria-selected', 'true');
            }
            
            // Mettre à jour les boutons de navigation
            updateNavigationButtons();
            
            return true;
        }
        
        // Fonction pour mettre à jour les boutons de navigation
        function updateNavigationButtons() {
            const currentTabId = activeTabInput.value;
            const currentIndex = tabs.indexOf(currentTabId);
            const isFirst = currentIndex === 0;
            const isLast = currentIndex === tabs.length - 1;
            
            const btnPrevious = document.getElementById('btn-previous');
            const btnNext = document.getElementById('btn-next');
            const btnSubmit = document.getElementById('btn-submit');
            
            if (btnPrevious) {
                btnPrevious.style.display = isFirst ? 'none' : '';
            }
            
            if (btnNext && isLast) {
                btnNext.style.display = 'none';
            }
            
            if (btnSubmit && !isLast) {
                btnSubmit.style.display = 'none';
            }
        }
        
        // Navigation précédent/suivant
        function navigateTab(direction) {
            const currentTabId = activeTabInput.value;
            const currentIndex = tabs.indexOf(currentTabId);
            
            if (direction === 'next' && currentIndex < tabs.length - 1) {
                const nextTabId = tabs[currentIndex + 1];
                const nextButton = document.querySelector(`[role="tab"][data-tab-id="${nextTabId}"]`);
                return switchTab(nextTabId, nextButton);
            } else if (direction === 'previous' && currentIndex > 0) {
                const prevTabId = tabs[currentIndex - 1];
                const prevButton = document.querySelector(`[role="tab"][data-tab-id="${prevTabId}"]`);
                return switchTab(prevTabId, prevButton, true);
            }
            
            return false;
        }
        
        // Écouter les clics sur les onglets
        tabButtons.forEach((button) => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const tabId = button.dataset.tabId;
                if (tabId) {
                    switchTab(tabId, button);
                }
            });
        });
        
        // Boutons de navigation
        const btnPrevious = document.getElementById('btn-previous');
        const btnNext = document.getElementById('btn-next');
        
        if (btnPrevious) {
            btnPrevious.addEventListener('click', function(e) {
                e.preventDefault();
                navigateTab('previous');
            });
        }
        
        if (btnNext) {
            btnNext.addEventListener('click', function(e) {
                e.preventDefault();
                navigateTab('next');
            });
        }
        
        // Validation avant soumission
        if (form) {
            form.addEventListener('submit', function(e) {
                let hasErrors = false;
                
                tabs.forEach(tabId => {
                    const validation = validateTab(tabId);
                    if (!validation.valid) {
                        hasErrors = true;
                        // Aller au premier onglet avec erreur
                        if (activeTabInput.value !== tabId) {
                            const tabButton = document.querySelector(`[role="tab"][data-tab-id="${tabId}"]`);
                            switchTab(tabId, tabButton, true);
                        }
                    }
                });
                
                if (hasErrors) {
                    e.preventDefault();
                    updateTabBadges();
                    return false;
                }
            });
        }
        
        // Initialiser les badges et la navigation
        updateTabBadges();
        updateNavigationButtons();
        
        // Mettre à jour les badges lors des changements de champs
        form?.addEventListener('input', function() {
            updateTabBadges();
        });
        
        // Log de débogage
        console.log('[Form with tabs] Initialized successfully', {
            tabs: tabs,
            currentTab: activeTabInput.value,
            tabButtons: tabButtons.length,
            btnNext: document.getElementById('btn-next') ? 'found' : 'not found',
            btnPrevious: document.getElementById('btn-previous') ? 'found' : 'not found',
            btnSubmit: document.getElementById('btn-submit') ? 'found' : 'not found'
        });
    }
    
    // Initialiser quand le DOM est prêt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFormWithTabs);
    } else {
        // DOM déjà chargé
        initFormWithTabs();
    }
})();
</script>
@endpush

