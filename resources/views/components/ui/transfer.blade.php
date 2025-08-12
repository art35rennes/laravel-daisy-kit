@props([
    // Données
    'source' => [],          // [[ 'data' => 'Label', 'disabled' => false, 'checked' => false, 'customId' => null ], ...]
    'target' => [],
    // Options
    'oneWay' => false,
    'pagination' => false,
    'elementsPerPage' => 5,
    'search' => false,
    'selectAll' => true,
    'noDataText' => 'No Data',
    // Personnalisation des textes
    'titleSource' => 'Source',
    'titleTarget' => 'Target',
    'selectAllTextSource' => 'Sélectionner tout',
    'selectAllTextTarget' => 'Sélectionner tout',
    'searchPlaceholderSource' => null,
    'searchPlaceholderTarget' => null,
    'toTargetButtonText' => null,
    'toSourceButtonText' => null,
    'toTargetArrow' => '→',
    'toSourceArrow' => '←',
    // Classes
    'class' => '',
])

@php
    $wrapAttrs = [
        'data-transfer' => '1',
        'data-one-way' => $oneWay ? 'true' : 'false',
        'data-pagination' => $pagination ? 'true' : 'false',
        'data-elements-per-page' => (string) $elementsPerPage,
        'data-search' => $search ? 'true' : 'false',
        'data-select-all' => $selectAll ? 'true' : 'false',
        'data-no-data-text' => $noDataText,
    ];
    
    // Génération des textes par défaut
    $defaultSearchPlaceholderSource = $searchPlaceholderSource ?? 'Rechercher dans ' . $titleSource;
    $defaultSearchPlaceholderTarget = $searchPlaceholderTarget ?? 'Rechercher dans ' . $titleTarget;
    $defaultToTargetButtonText = $toTargetButtonText ?? $titleSource . ' ' . $toTargetArrow . ' ' . $titleTarget;
    $defaultToSourceButtonText = $toSourceButtonText ?? $titleTarget . ' ' . $toSourceArrow . ' ' . $titleSource;
@endphp

<div {{ $attributes->merge(['class' => trim('w-full '.$class)])->merge($wrapAttrs) }}>
    <div class="flex flex-wrap -mx-3">
        {{-- Source panel --}}
        <div class="w-full sm:w-full md:w-1/2 lg:w-5/12 xl:w-2/5 px-3 mb-6 md:mb-4 lg:mb-0">
            {{-- Source header --}}
            <div class="flex flex-col space-y-2 sm:flex-row sm:items-center sm:justify-between sm:space-y-0 mb-3">
                <h3 class="text-base font-semibold">{{ $titleSource }}</h3>
                @if($selectAll)
                    <div class="flex items-center gap-2 shrink-0">
                        <input type="checkbox" class="checkbox checkbox-sm" data-transfer-selectall="source" id="select-all-source-{{ uniqid() }}" />
                        <label for="select-all-source-{{ uniqid() }}" class="text-sm cursor-pointer whitespace-nowrap">{{ $selectAllTextSource }}</label>
                    </div>
                @endif
            </div>
            
            {{-- Source content --}}
            <div class="card bg-base-100 border border-base-300 h-fit">
                <div class="card-body p-3 space-y-3">
                    @if($search)
                        <input type="text" class="input input-sm input-bordered w-full" placeholder="{{ $defaultSearchPlaceholderSource }}" data-transfer-search="source" />
                    @endif
                    <ul class="menu menu-sm bg-base-100 rounded-box border border-base-300 overflow-auto min-h-32 max-h-64" data-transfer-list="source">
                        @foreach($source as $i => $it)
                            @php
                                $label = is_array($it) ? ($it['data'] ?? (string)$i) : (string)$it;
                                $disabled = is_array($it) ? !empty($it['disabled']) : false;
                                $checked = is_array($it) ? !empty($it['checked']) : false;
                                $customId = is_array($it) ? ($it['customId'] ?? null) : null;
                            @endphp
                            <li class="px-2" data-transfer-item data-id="{{ $customId ?? ('s-'.$i) }}" data-label="{{ $label }}" data-disabled="{{ $disabled ? 'true' : 'false' }}" data-checked="{{ $checked ? 'true' : 'false' }}">
                                <label class="flex items-center gap-2 py-1">
                                    <input type="checkbox" class="checkbox checkbox-xs" @checked($checked) @disabled($disabled) />
                                    <span class="truncate">{{ $label }}</span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                    @if($pagination)
                        <div class="join justify-center" data-transfer-pager="source">
                            <button type="button" class="btn btn-xs join-item" data-transfer-page="prev">«</button>
                            <span class="btn btn-xs join-item" data-transfer-page="info">1/1</span>
                            <button type="button" class="btn btn-xs join-item" data-transfer-page="next">»</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Controls --}}
        <div class="w-full sm:w-full md:w-full lg:w-2/12 xl:w-1/5 px-3 mb-6 md:mb-4 lg:mb-0 order-3 md:order-2">
            <div class="flex flex-row lg:flex-col items-center justify-center gap-3 h-full min-h-16 lg:min-h-32">
                <button type="button" class="btn btn-sm lg:btn-square lg:btn-md xl:btn-lg flex-1 lg:flex-none" data-transfer-move="toTarget">
                    <span class="lg:hidden text-xs">{{ $defaultToTargetButtonText }}</span>
                    <span class="hidden lg:inline text-lg xl:text-xl">{{ $toTargetArrow }}</span>
                </button>
                @if(!$oneWay)
                    <button type="button" class="btn btn-sm lg:btn-square lg:btn-md xl:btn-lg flex-1 lg:flex-none" data-transfer-move="toSource">
                        <span class="lg:hidden text-xs">{{ $defaultToSourceButtonText }}</span>
                        <span class="hidden lg:inline text-lg xl:text-xl">{{ $toSourceArrow }}</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Target panel --}}
        <div class="w-full sm:w-full md:w-1/2 lg:w-5/12 xl:w-2/5 px-3 order-2 md:order-3">
            {{-- Target header --}}
            <div class="flex flex-col space-y-2 sm:flex-row sm:items-center sm:justify-between sm:space-y-0 mb-3">
                <h3 class="text-base font-semibold">{{ $titleTarget }}</h3>
                @if($selectAll)
                    <div class="flex items-center gap-2 shrink-0">
                        <input type="checkbox" class="checkbox checkbox-sm" data-transfer-selectall="target" id="select-all-target-{{ uniqid() }}" />
                        <label for="select-all-target-{{ uniqid() }}" class="text-sm cursor-pointer whitespace-nowrap">{{ $selectAllTextTarget }}</label>
                    </div>
                @endif
            </div>
            
            {{-- Target content --}}
            <div class="card bg-base-100 border border-base-300 h-fit">
                <div class="card-body p-3 space-y-3">
                    @if($search)
                        <input type="text" class="input input-sm input-bordered w-full" placeholder="{{ $defaultSearchPlaceholderTarget }}" data-transfer-search="target" />
                    @endif
                    <ul class="menu menu-sm bg-base-100 rounded-box border border-base-300 overflow-auto min-h-32 max-h-64" data-transfer-list="target">
                        @foreach($target as $i => $it)
                            @php
                                $label = is_array($it) ? ($it['data'] ?? (string)$i) : (string)$it;
                                $disabled = is_array($it) ? !empty($it['disabled']) : false;
                                $checked = is_array($it) ? !empty($it['checked']) : false;
                                $customId = is_array($it) ? ($it['customId'] ?? null) : null;
                            @endphp
                            <li class="px-2" data-transfer-item data-id="{{ $customId ?? ('t-'.$i) }}" data-label="{{ $label }}" data-disabled="{{ $disabled ? 'true' : 'false' }}" data-checked="{{ $checked ? 'true' : 'false' }}">
                                <label class="flex items-center gap-2 py-1">
                                    <input type="checkbox" class="checkbox checkbox-xs" @checked($checked) @disabled($disabled) />
                                    <span class="truncate">{{ $label }}</span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                    @if($pagination)
                        <div class="join justify-center" data-transfer-pager="target">
                            <button type="button" class="btn btn-xs join-item" data-transfer-page="prev">«</button>
                            <span class="btn btn-xs join-item" data-transfer-page="info">1/1</span>
                            <button type="button" class="btn btn-xs join-item" data-transfer-page="next">»</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


