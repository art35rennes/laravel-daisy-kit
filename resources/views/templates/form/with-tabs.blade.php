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
])

@php
    // Déterminer l'onglet actif
    $currentActiveTab = $activeTab ?? old('_active_tab', $tabs[0]['id'] ?? null);
    
    // Compter les erreurs par onglet si highlightErrors est activé
    $tabErrors = [];
    if ($highlightErrors && $errors->any()) {
        foreach ($tabs as $tab) {
            $tabId = $tab['id'] ?? null;
            if (!$tabId) {
                continue;
            }
            
            // Compter les erreurs pour les champs qui commencent par le préfixe de l'onglet
            // ou qui sont dans les champs spécifiés pour cet onglet
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
    
    // Préparer les items pour le composant tabs
    $tabsItems = [];
    foreach ($tabs as $tab) {
        $tabId = $tab['id'] ?? null;
        $label = $tab['label'] ?? 'Tab';
        $icon = $tab['icon'] ?? null;
        $errorCount = $tabErrors[$tabId] ?? 0;
        
        // Construire le label avec badge d'erreur si nécessaire
        $displayLabel = $label;
        if ($errorCount > 0) {
            $displayLabel = $label . ' <span class="badge badge-error badge-sm ml-2">' . $errorCount . '</span>';
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
            <x-daisy::ui.navigation.tabs 
                :items="$tabsItems" 
                :variant="$tabsVariant"
                :placement="$tabsPlacement"
            />

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
            <div class="flex items-center justify-end gap-3">
                @isset($actions)
                    {{ $actions }}
                @else
                    <x-daisy::ui.inputs.button type="button" variant="ghost" onclick="window.history.back()">
                        {{ __('form.cancel') }}
                    </x-daisy::ui.inputs.button>
                    <x-daisy::ui.inputs.button type="submit">
                        {{ __('form.save') }}
                    </x-daisy::ui.inputs.button>
                @endisset
            </div>
        </form>
    </div>
</x-daisy::layout.app>

@push('scripts')
<script>
    (function() {
        // Mettre à jour le champ caché _active_tab lors du changement d'onglet
        const activeTabInput = document.getElementById('active-tab-input');
        if (!activeTabInput) return;

        // Écouter les clics sur les onglets
        const tabButtons = document.querySelectorAll('[role="tablist"] button[role="tab"], [role="tablist"] a[role="tab"]');
        const tabContents = document.querySelectorAll('[data-tab-content]');

        tabButtons.forEach((button, index) => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Trouver l'ID de l'onglet correspondant
                const tabs = @json($tabs);
                if (tabs[index]) {
                    const tabId = tabs[index]['id'];
                    if (tabId) {
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
                        tabButtons.forEach(btn => btn.classList.remove('tab-active'));
                        button.classList.add('tab-active');
                    }
                }
            });
        });
    })();
</script>
@endpush

