@props([
    'action' => '#',
    'method' => 'GET',
    'size' => 'sm', // xs|sm|md
    'collapseBelow' => 'md',
    'showReset' => true,
    'submitText' => __('daisy::form.search'),
    'resetText' => __('daisy::form.reset'),
    'activeFilters' => [], // [['label' => 'Statut', 'value' => 'Actif', 'param' => 'status']]
    'showAdvanced' => false,
    'advancedTitle' => __('daisy::form.advanced_filters'),
    'autoRefreshCsrf' => true,
])

@php
    $isGet = strtoupper($method) === 'GET';
    $sizeClasses = match($size) {
        'xs' => 'input-xs',
        'sm' => 'input-sm',
        'md' => 'input-md',
        default => 'input-sm',
    };
    $buttonSizeClasses = match($size) {
        'xs' => 'btn-xs',
        'sm' => 'btn-sm',
        'md' => 'btn-md',
        default => 'btn-sm',
    };
@endphp

@php
    // Générer un ID unique pour cette instance si non fourni
    $instanceId = $attributes->get('id') ?? 'form-inline-'.uniqid();
@endphp

<form 
    id="{{ $instanceId }}"
    action="{{ $action }}" 
    method="{{ strtoupper($method) }}" 
    data-module="inline"
    data-inline-instance-id="{{ $instanceId }}"
    class="space-y-4"
    {{ $attributes->except(['id']) }}
>
    @if(!$isGet)
        @csrf
    @endif
    
    @if(!$isGet && strtoupper($method) !== 'POST')
        @method($method)
    @endif
    
    @if($autoRefreshCsrf && !$isGet)
        <x-daisy::ui.utilities.csrf-keeper />
    @endif
    
    {{-- Filtres actifs (tokens) --}}
    @if(!empty($activeFilters))
        <div class="flex flex-wrap items-center gap-2" data-active-filters>
            @foreach($activeFilters as $filter)
                <div class="badge badge-lg gap-2 {{ $sizeClasses }}">
                    <span>{{ $filter['label'] ?? '' }}: <strong>{{ $filter['value'] ?? '' }}</strong></span>
                    <button 
                        type="button" 
                        class="btn btn-ghost btn-xs btn-circle"
                        data-filter-clear
                        data-filter-param="{{ $filter['param'] ?? '' }}"
                        aria-label="{{ __('daisy::form.clear_filter') }}"
                    >
                        <x-bi-x class="w-3 h-3" />
                    </button>
                </div>
            @endforeach
        </div>
    @endif
    
    {{-- Filtres principaux --}}
    <div class="flex flex-col {{ $collapseBelow }}:flex-row gap-3 items-end {{ $collapseBelow }}:items-center">
        <div class="flex-1 grid grid-cols-1 {{ $collapseBelow }}:grid-cols-2 lg:grid-cols-3 gap-3 w-full">
            @isset($filters)
                {!! $filters !!}
            @endisset
        </div>
        
        <div class="flex items-center gap-2 flex-shrink-0">
            @if($showAdvanced)
                <button 
                    type="button" 
                    class="btn {{ $buttonSizeClasses }} btn-ghost"
                    onclick="document.getElementById('form-inline-advanced-drawer').checked = true"
                >
                    {{ $advancedTitle }}
                </button>
            @endif
            
            @if($showReset)
                <a 
                    href="{{ $action }}" 
                    class="btn {{ $buttonSizeClasses }} btn-ghost"
                >
                    {{ $resetText }}
                </a>
            @endif
            
            <x-daisy::ui.inputs.button type="submit" :size="$size" variant="solid">
                {{ $submitText }}
            </x-daisy::ui.inputs.button>
        </div>
    </div>
    
    {{-- Actions supplémentaires --}}
    @isset($actions)
        <div class="flex items-center gap-2">
            {!! $actions !!}
        </div>
    @endisset
</form>

{{-- Drawer pour les filtres avancés --}}
@if($showAdvanced)
    <x-daisy::ui.overlay.drawer 
        id="form-inline-advanced-drawer"
        :sideIsMenu="false"
        sideClass="w-96"
    >
        <x-slot:side>
            <div class="p-6 space-y-4">
                <h3 class="text-lg font-semibold">{{ $advancedTitle }}</h3>
                @isset($advanced)
                    {!! $advanced !!}
                @endisset
            </div>
        </x-slot:side>
    </x-daisy::ui.overlay.drawer>
@endif


