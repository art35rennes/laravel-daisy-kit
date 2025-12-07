@props([
    'title' => __('daisy::form.tabs.title'),
    'action' => '#',
    'method' => 'POST',
    'tabs' => [], // [['id' => 'general', 'label' => 'Général']]
    'activeTab' => null,
    'tabsStyle' => 'box', // box|border|lift
    'tabsPlacement' => 'top', // top|bottom
    'highlightErrors' => true,
    'showErrorBadges' => true,
    'persistActiveTabField' => '_active_tab',
    'fieldToTabMap' => [], // Mapping des champs vers les onglets pour le comptage d'erreurs
    'autoRefreshCsrf' => true,
])

@php
    use Art35rennes\DaisyKit\Helpers\TabErrorBag;
    
    // Déterminer l'onglet actif
    $currentActiveTab = $activeTab ?? old($persistActiveTabField) ?? ($tabs[0]['id'] ?? null);
    
    // Compter les erreurs par onglet si nécessaire
    $errorCountsByTab = [];
    $errorsBag = $errors ?? new \Illuminate\Support\MessageBag();
    if ($showErrorBadges && $highlightErrors && !empty($fieldToTabMap) && $errorsBag->any()) {
        $errorCountsByTab = TabErrorBag::countErrorsByTab($fieldToTabMap, $errorsBag);
    } elseif ($showErrorBadges && $highlightErrors && $errorsBag->any()) {
        // Fallback : utiliser les préfixes si aucun mapping n'est fourni
        $tabIds = array_column($tabs, 'id');
        $errorCountsByTab = TabErrorBag::countErrorsByTabPrefix($tabIds, $errorsBag);
    }
    
    // Construire les items pour le composant tabs
    $tabItems = [];
    foreach ($tabs as $tab) {
        $tabId = $tab['id'] ?? null;
        $tabLabel = $tab['label'] ?? 'Tab';
        $isActive = $tabId === $currentActiveTab;
        
        // Ajouter un badge d'erreur si nécessaire
        $errorCount = $errorCountsByTab[$tabId] ?? 0;
        
        $tabItems[] = [
            'id' => $tabId,
            'label' => $tabLabel,
            'errorCount' => $errorCount,
            'active' => $isActive,
        ];
    }
    
    // Déterminer le style des tabs
    $tabsVariant = match($tabsStyle) {
        'box' => 'box',
        'border' => 'border',
        'lift' => 'lifted',
        default => 'box',
    };
@endphp

@php
    // Générer un ID unique pour cette instance si non fourni
    $instanceId = $attributes->get('id') ?? 'form-tabs-'.uniqid();
@endphp

<form 
    id="{{ $instanceId }}"
    action="{{ $action }}" 
    method="{{ strtoupper($method) }}" 
    data-module="tabs"
    data-tabs-instance-id="{{ $instanceId }}"
    data-persist-field="{{ $persistActiveTabField }}"
    {{ $attributes->except(['id']) }}
>
    @if(strtoupper($method) !== 'GET')
        @csrf
    @endif
    
    @if(strtoupper($method) !== 'GET' && strtoupper($method) !== 'POST')
        @method($method)
    @endif
    
    <input type="hidden" name="{{ $persistActiveTabField }}" value="{{ $currentActiveTab }}" />
    
    @if($autoRefreshCsrf && strtoupper($method) !== 'GET')
        <x-daisy::ui.utilities.csrf-keeper />
    @endif
    
    @if($title)
        <h2 class="text-2xl font-semibold mb-6">{{ $title }}</h2>
    @endif
    
    <div class="space-y-6">
        @php
            $tabsRadioName = 'form-tabs-'.uniqid();
        @endphp
        
        {{-- Tabs navigation avec contenu --}}
        <div class="tabs {{ $tabsVariant === 'box' ? 'tabs-box' : ($tabsVariant === 'border' ? 'tabs-border' : 'tabs-lift') }} {{ $tabsPlacement === 'bottom' ? 'tabs-bottom' : 'tabs-top' }}">
            @foreach($tabs as $index => $tab)
                @php
                    $tabId = $tab['id'] ?? null;
                    $tabLabel = $tab['label'] ?? 'Tab';
                    $isActive = $tabId === $currentActiveTab;
                    $errorCount = $errorCountsByTab[$tabId] ?? 0;
                @endphp
                <input 
                    type="radio" 
                    name="{{ $tabsRadioName }}" 
                    class="tab" 
                    aria-label="{{ $tabLabel }}"
                    @checked($isActive)
                    data-tab-id="{{ $tabId }}"
                />
                <div class="tab-content border-base-300 bg-base-100 p-6" data-tab-content-id="{{ $tabId }}" role="tabpanel">
                    {{-- Header de l'onglet avec badge d'erreur --}}
                    @if($showErrorBadges && $errorCount > 0)
                        <div class="flex items-center gap-2 mb-4">
                            <h3 class="text-lg font-semibold">{{ $tabLabel }}</h3>
                            <x-daisy::ui.data-display.badge color="error" size="sm">{{ $errorCount }}</x-daisy::ui.data-display.badge>
                        </div>
                    @endif
                    
                    {{-- Contenu principal de l'onglet --}}
                    @if(isset(${'tab_'.$tabId}))
                        {!! ${'tab_'.$tabId} !!}
                    @endif
                    
                    {{-- Footer optionnel de l'onglet --}}
                    @if(isset(${'tab_'.$tabId.'_footer'}))
                        <div class="mt-6 pt-6 border-t">
                            {!! ${'tab_'.$tabId.'_footer'} !!}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        
        {{-- Actions du formulaire --}}
        @isset($actions)
            <div class="flex items-center justify-end gap-3 pt-6 border-t">
                {!! $actions !!}
            </div>
        @endisset
    </div>
</form>


