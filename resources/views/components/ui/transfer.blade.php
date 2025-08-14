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
    // Style des boutons de transfert
    'buttonsColor' => 'primary',   // primary | secondary | accent | neutral | info | success | warning | error
    'buttonsSize' => 'md',         // sm | md | lg
    'buttonsVariant' => 'solid',   // solid | outline | ghost
    'buttonsMode' => 'text',       // text | icon | both
    'tooltip' => true,             // true => tooltip DaisyUI au survol (utile en mode icon)
    'tooltipPlacement' => 'top',   // top | right | bottom | left
    // Responsivité et overflow
    'stackOn' => 'md',             // sm | md | lg | xl (breakpoint de passage en colonnes)
    'listOverflow' => 'y',         // y | x | both | none
    'listMaxHeight' => 'max-h-64', // classe Tailwind appliquée quand overflow-y
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
        'data-stack-on' => $stackOn,
        'data-list-overflow' => $listOverflow,
    ];
    
    // Génération des textes par défaut
    $defaultSearchPlaceholderSource = $searchPlaceholderSource ?? 'Rechercher dans ' . $titleSource;
    $defaultSearchPlaceholderTarget = $searchPlaceholderTarget ?? 'Rechercher dans ' . $titleTarget;
    $defaultToTargetButtonText = $toTargetButtonText ?? $titleSource . ' ' . $toTargetArrow . ' ' . $titleTarget;
    $defaultToSourceButtonText = $toSourceButtonText ?? $titleTarget . ' ' . $toSourceArrow . ' ' . $titleSource;

    // IDs uniques (stables) pour les cases "tout sélectionner"
    $sourceSelectAllId = 'select-all-source-'.uniqid();
    $targetSelectAllId = 'select-all-target-'.uniqid();

    // Classes boutons (couleur/taille/forme)
    $btnColor = in_array($buttonsColor, ['primary','secondary','accent','neutral','info','success','warning','error']) ? $buttonsColor : 'primary';
    $btnSize = in_array($buttonsSize, ['sm','md','lg']) ? $buttonsSize : 'md';
    $btnVariantClass = $buttonsVariant === 'outline' ? ' btn-outline' : ($buttonsVariant === 'ghost' ? ' btn-ghost' : '');
    $useText = ($buttonsMode === 'text') || ($buttonsMode === 'both');
    $useIcon = ($buttonsMode === 'icon') || ($buttonsMode === 'both');
    $btnBase = 'btn btn-'.$btnColor.' btn-'.$btnSize.$btnVariantClass.($useIcon && !$useText ? ' btn-circle' : '');
    $tooltipClass = 'tooltip tooltip-'.(in_array($tooltipPlacement, ['top','right','bottom','left']) ? $tooltipPlacement : 'top');

    // Classes overflow pour les UL
    $overflowClasses = match($listOverflow) {
        'x' => 'overflow-x-auto whitespace-nowrap',
        'both' => 'overflow-auto whitespace-nowrap '.$listMaxHeight,
        'none' => 'overflow-visible',
        default => 'overflow-y-auto '.$listMaxHeight,
    };

    // Breakpoint type Bootstrap (row/col) basé sur grid 12 colonnes
    $break = in_array($stackOn, ['sm','md','lg','xl']) ? $stackOn : 'md';
@endphp

<div {{ $attributes->merge(['class' => trim('w-full '.$class)])->merge($wrapAttrs) }}>
    <div class="grid grid-cols-1 {{ $break }}:grid-cols-12 gap-4 items-stretch w-full">
        {{-- Source panel --}}
        <div class="card bg-base-100 border border-base-300 h-full min-w-0 col-span-12 {{ $break }}:col-span-5">
            <div class="card-body p-3 space-y-3">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        @if($selectAll)
                            <label class="label cursor-pointer gap-2">
                                <input type="checkbox" class="checkbox checkbox-sm" data-transfer-selectall="source" id="{{ $sourceSelectAllId }}">
                                <span class="font-semibold">{{ $titleSource }}</span>
                            </label>
                        @else
                            <h3 class="font-semibold">{{ $titleSource }}</h3>
                        @endif
                    </div>
                    <span class="text-sm opacity-70" data-transfer-count="source"></span>
                </div>

                @if($search)
                    <div class="join w-full">
                        <input type="text" class="input input-sm input-bordered join-item w-full" placeholder="{{ $defaultSearchPlaceholderSource }}" data-transfer-search="source" />
                    </div>
                @endif

                <ul class="menu menu-sm w-full bg-base-100 rounded-box border border-base-300 {{ $overflowClasses }}" data-transfer-list="source">
                    @foreach($source as $i => $it)
                        @php
                            $label = is_array($it) ? ($it['data'] ?? (string)$i) : (string)$it;
                            $disabled = is_array($it) ? !empty($it['disabled']) : false;
                            $checked = is_array($it) ? !empty($it['checked']) : false;
                            $customId = is_array($it) ? ($it['customId'] ?? null) : null;
                        @endphp
                        <li data-transfer-item data-id="{{ $customId ?? ('s-'.$i) }}" data-label="{{ $label }}" data-disabled="{{ $disabled ? 'true' : 'false' }}" data-checked="{{ $checked ? 'true' : 'false' }}">
                            <label class="label cursor-pointer">
                                <input type="checkbox" class="checkbox checkbox-sm" @checked($checked) @disabled($disabled) />
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

        {{-- Transfer controls --}}
        <div class="col-span-12 {{ $break }}:col-span-2 flex flex-col justify-center items-center gap-3">
            @php 
                $toTargetContent = '';
                if ($useIcon) {
                    $toTargetContent .= '<span class="text-lg inline '.$break.':hidden">↓</span>';
                    $toTargetContent .= '<span class="text-lg hidden '.$break.':inline">'.e($toTargetArrow).'</span>';
                }
                if ($useText) $toTargetContent .= '<span class="'.($useIcon ? 'ml-2 ' : '').' whitespace-nowrap">'.e($defaultToTargetButtonText).'</span>';
                $toTargetBtn = (
                    '<button type="button" class="'.$btnBase.'" data-transfer-move="toTarget" aria-label="'.e($defaultToTargetButtonText).'">'
                    .$toTargetContent
                    .'</button>'
                ); 
            @endphp
            @if($tooltip && !$useText && $useIcon)
                <div class="{{ $tooltipClass }}" data-tip="{{ $defaultToTargetButtonText }}">{!! $toTargetBtn !!}</div>
            @else
                {!! $toTargetBtn !!}
            @endif

            @if(!$oneWay)
                @php 
                    $toSourceContent = '';
                    if ($useIcon) {
                        $toSourceContent .= '<span class="text-lg inline '.$break.':hidden">↑</span>';
                        $toSourceContent .= '<span class="text-lg hidden '.$break.':inline">'.e($toSourceArrow).'</span>';
                    }
                    if ($useText) $toSourceContent .= '<span class="'.($useIcon ? 'ml-2 ' : '').' whitespace-nowrap">'.e($defaultToSourceButtonText).'</span>';
                    $toSourceBtn = (
                        '<button type="button" class="'.$btnBase.'" data-transfer-move="toSource" aria-label="'.e($defaultToSourceButtonText).'">'
                        .$toSourceContent
                        .'</button>'
                    ); 
                @endphp
                @if($tooltip && !$useText && $useIcon)
                    <div class="{{ $tooltipClass }}" data-tip="{{ $defaultToSourceButtonText }}">{!! $toSourceBtn !!}</div>
                @else
                    {!! $toSourceBtn !!}
                @endif
            @endif
        </div>

        {{-- Target panel --}}
        <div class="card bg-base-100 border border-base-300 h-full min-w-0 col-span-12 {{ $break }}:col-span-5">
            <div class="card-body p-3 space-y-3">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        @if($selectAll)
                            <label class="label cursor-pointer gap-2">
                                <input type="checkbox" class="checkbox checkbox-sm" data-transfer-selectall="target" id="{{ $targetSelectAllId }}">
                                <span class="font-semibold">{{ $titleTarget }}</span>
                            </label>
                        @else
                            <h3 class="font-semibold">{{ $titleTarget }}</h3>
                        @endif
                    </div>
                    <span class="text-sm opacity-70" data-transfer-count="target"></span>
                </div>

                @if($search)
                    <div class="join w-full">
                        <input type="text" class="input input-sm input-bordered join-item w-full" placeholder="{{ $defaultSearchPlaceholderTarget }}" data-transfer-search="target" />
                    </div>
                @endif

                <ul class="menu menu-sm w-full bg-base-100 rounded-box border border-base-300 {{ $overflowClasses }}" data-transfer-list="target">
                    @foreach($target as $i => $it)
                        @php
                            $label = is_array($it) ? ($it['data'] ?? (string)$i) : (string)$it;
                            $disabled = is_array($it) ? !empty($it['disabled']) : false;
                            $checked = is_array($it) ? !empty($it['checked']) : false;
                            $customId = is_array($it) ? ($it['customId'] ?? null) : null;
                        @endphp
                        <li data-transfer-item data-id="{{ $customId ?? ('t-'.$i) }}" data-label="{{ $label }}" data-disabled="{{ $disabled ? 'true' : 'false' }}" data-checked="{{ $checked ? 'true' : 'false' }}">
                            <label class="label cursor-pointer">
                                <input type="checkbox" class="checkbox checkbox-sm" @checked($checked) @disabled($disabled) />
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


@include('daisy::components.partials.assets')
