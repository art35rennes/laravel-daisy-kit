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
    'titleSource' => 'Source',
    'titleTarget' => 'Target',
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
@endphp

<div {{ $attributes->merge(['class' => trim('grid grid-cols-1 md:grid-cols-3 gap-4 items-stretch '.$class)])->merge($wrapAttrs) }}>
    {{-- Source panel --}}
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body p-3 gap-2">
            <div class="flex items-center justify-between">
                <div class="font-medium">{{ $titleSource }}</div>
                @if($selectAll)
                    <label class="label cursor-pointer gap-2 text-sm">
                        <span>Sélectionner tout</span>
                        <input type="checkbox" class="checkbox checkbox-sm" data-transfer-selectall="source" />
                    </label>
                @endif
            </div>
            @if($search)
                <input type="text" class="input input-sm input-bordered w-full" placeholder="Rechercher" data-transfer-search="source" />
            @endif
            <ul class="menu menu-sm bg-base-100 rounded-box border border-base-300 overflow-auto max-h-64" data-transfer-list="source">
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

    {{-- Controls --}}
    <div class="flex flex-col items-center justify-center gap-2">
        <button type="button" class="btn btn-sm" data-transfer-move="toTarget">{{ $toTargetArrow }}</button>
        <button type="button" class="btn btn-sm" data-transfer-move="toSource">{{ $toSourceArrow }}</button>
    </div>

    {{-- Target panel --}}
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body p-3 gap-2">
            <div class="flex items-center justify-between">
                <div class="font-medium">{{ $titleTarget }}</div>
                @if($selectAll)
                    <label class="label cursor-pointer gap-2 text-sm">
                        <span>Sélectionner tout</span>
                        <input type="checkbox" class="checkbox checkbox-sm" data-transfer-selectall="target" />
                    </label>
                @endif
            </div>
            @if($search)
                <input type="text" class="input input-sm input-bordered w-full" placeholder="Rechercher" data-transfer-search="target" />
            @endif
            <ul class="menu menu-sm bg-base-100 rounded-box border border-base-300 overflow-auto max-h-64" data-transfer-list="target">
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


