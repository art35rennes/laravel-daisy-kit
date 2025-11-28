@props([
    'items' => [], // [ { when,title,content, icon, boxOn: 'start'|'end'|null, hrBefore?:bool, hrAfter?:bool, startHtml?, endHtml? } ]
    'orientation' => 'vertical', // vertical|horizontal (daisyUI: horizontal par défaut)
    'compact' => false,
    'snapIcon' => false, // timeline-snap-icon (icône alignée sur start)
    // Valeur par défaut pour appliquer timeline-box sur un côté (item.boxOn a priorité)
    'boxOn' => 'end', // start|end|null
])

@php
    // Construction des classes CSS selon l'orientation et les options (compact, snapIcon).
    $classes = 'timeline';
    $classes .= $orientation === 'horizontal' ? ' timeline-horizontal' : ' timeline-vertical';
    if ($compact) $classes .= ' timeline-compact';
    if ($snapIcon) $classes .= ' timeline-snap-icon';
@endphp

<ul {{ $attributes->merge(['class' => $classes]) }}>
    @foreach($items as $index => $item)
        <li>
            @php
                // Détermination de l'application de timeline-box : priorité à item.boxOn, sinon boxOn global.
                $applyBox = $item['boxOn'] ?? $boxOn;
                $startClasses = 'timeline-start'.($applyBox === 'start' ? ' timeline-box' : '');
                $endClasses = 'timeline-end'.($applyBox === 'end' ? ' timeline-box' : '');
                // Détection du dernier item pour la gestion des séparateurs.
                $isLast = $index === (count($items) - 1);
                // Logique des séparateurs : hrBefore explicite OU automatique si index > 0.
                $hrBefore = array_key_exists('hrBefore', $item) ? (bool)$item['hrBefore'] : ($index > 0);
                // hrAfter explicite OU automatique si ce n'est pas le dernier item.
                $hrAfter = array_key_exists('hrAfter', $item) ? (bool)$item['hrAfter'] : (!$isLast);
            @endphp

            {{-- Séparateur avant l'item (optionnel) --}}
            @if($hrBefore)
                <hr />
            @endif

            {{-- Colonne start : date/heure ou contenu HTML personnalisé --}}
            <div class="{{ $startClasses }}">
                @if(!empty($item['startHtml']))
                    {!! $item['startHtml'] !!}
                @else
                    {{ $item['when'] ?? '' }}
                @endif
            </div>

            {{-- Colonne middle : icône (personnalisée ou par défaut) --}}
            <div class="timeline-middle">
                @if(!empty($item['icon']))
                    {!! $item['icon'] !!}
                @else
                    {{-- Icône par défaut : checkmark (timeline de succès) --}}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                @endif
            </div>

            {{-- Colonne end : titre et contenu, ou HTML personnalisé --}}
            <div class="{{ $endClasses }}">
                @if(!empty($item['endHtml']))
                    {!! $item['endHtml'] !!}
                @else
                    @if(!empty($item['title']))
                        <div class="text-lg font-black">{{ $item['title'] }}</div>
                    @endif
                    @if(isset($item['content']))
                        <div>{{ $item['content'] }}</div>
                    @endif
                @endif
            </div>

            {{-- Séparateur après l'item (optionnel) --}}
            @if($hrAfter)
                <hr />
            @endif
        </li>
    @endforeach
</ul>
