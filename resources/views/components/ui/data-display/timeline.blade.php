@props([
    'items' => [], // [ { when,title,content, iconName?, iconHtml?, icon?, boxOn: 'start'|'end'|null, hrBefore?:bool, hrAfter?, startHtml?, endHtml? } ]
    'orientation' => 'vertical', // vertical|horizontal (daisyUI: horizontal par défaut)
    'responsiveAt' => 'lg', // used when orientation is responsive
    'compact' => false,
    'snapIcon' => false, // timeline-snap-icon (icône alignée sur start)
    'showIcons' => true,
    'side' => 'both', // both|start|end|alternate
    'lineClass' => null,
    'iconClass' => 'h-5 w-5',
    // Valeur par défaut pour appliquer timeline-box sur un côté (item.boxOn a priorité)
    'boxOn' => 'end', // start|end|null
])

@php
    // Construction des classes CSS selon l'orientation et les options (compact, snapIcon).
    $classes = 'timeline';
    $classes .= match ($orientation) {
        'horizontal' => ' timeline-horizontal',
        'responsive' => ' timeline-vertical '.$responsiveAt.':timeline-horizontal',
        default => ' timeline-vertical',
    };
    if ($compact) $classes .= ' timeline-compact';
    if ($snapIcon) $classes .= ' timeline-snap-icon';

    $itemValue = static function (array $item, array $keys, mixed $default = null): mixed {
        foreach ($keys as $key) {
            if (array_key_exists($key, $item)) {
                return $item[$key];
            }
        }

        return $default;
    };

    $hasVisibleValue = static fn (mixed $value): bool => $value !== null && $value !== '';
@endphp

<ul {{ $attributes->merge(['class' => $classes]) }}>
    @foreach($items as $index => $item)
        <li>
            @php
                // Détermination de l'application de timeline-box : priorité à item.boxOn, sinon boxOn global.
                $applyBox = $item['boxOn'] ?? $boxOn;
                $itemSide = $item['side'] ?? $side;
                if ($itemSide === 'alternate') {
                    $itemSide = $index % 2 === 0 ? 'start' : 'end';
                }

                $rendersStart = $itemSide !== 'end';
                $rendersEnd = $itemSide !== 'start';
                $rendersIcon = array_key_exists('showIcon', $item) ? (bool) $item['showIcon'] : (bool) $showIcons;

                $startClasses = 'timeline-start'.($applyBox === 'start' ? ' timeline-box' : '');
                $endClasses = 'timeline-end'.($applyBox === 'end' ? ' timeline-box' : '');
                // Détection du dernier item pour la gestion des séparateurs.
                $isLast = $index === (count($items) - 1);
                // Logique des séparateurs : hrBefore explicite OU automatique si index > 0.
                $hrBefore = array_key_exists('hrBefore', $item) ? (bool)$item['hrBefore'] : ($index > 0);
                // hrAfter explicite OU automatique si ce n'est pas le dernier item.
                $hrAfter = array_key_exists('hrAfter', $item) ? (bool)$item['hrAfter'] : (!$isLast);
                $beforeClass = $item['hrBeforeClass'] ?? $item['lineClass'] ?? $lineClass;
                $afterClass = $item['hrAfterClass'] ?? $item['lineClass'] ?? $lineClass;
                $itemIconClass = trim($item['iconClass'] ?? $iconClass);

                $startHtml = $item['startHtml'] ?? null;
                $endHtml = $item['endHtml'] ?? null;
                $startText = $itemValue($item, ['start', 'when']);
                $endText = $itemValue($item, ['end', 'title', 'content']);

                if ($itemSide === 'start' && ! $hasVisibleValue($startHtml) && ! $hasVisibleValue($startText)) {
                    $startHtml = $endHtml;
                    $startText = $endText;
                }

                if ($itemSide === 'end' && ! $hasVisibleValue($endHtml) && ! $hasVisibleValue($endText)) {
                    $endHtml = $startHtml;
                    $endText = $startText;
                }
            @endphp

            {{-- Séparateur avant l'item (optionnel) --}}
            @if($hrBefore)
                <hr @if($beforeClass) class="{{ $beforeClass }}" @endif />
            @endif

            {{-- Colonne start : date/heure ou contenu HTML personnalisé --}}
            @if($rendersStart)
                <div class="{{ $startClasses }}">
                    @if($hasVisibleValue($startHtml))
                        {!! $startHtml !!}
                    @else
                        {{ $startText ?? '' }}
                    @endif
                </div>
            @endif

            {{-- Colonne middle : icône (personnalisée ou par défaut) --}}
            @if($rendersIcon)
                <div class="timeline-middle">
                    @if(!empty($item['iconName']))
                        <x-icon :name="$item['iconName']" :class="$itemIconClass" />
                    @elseif(!empty($item['iconHtml']))
                        {!! $item['iconHtml'] !!}
                    @elseif(!empty($item['icon']) && $item['icon'] instanceof \Illuminate\Contracts\Support\Htmlable)
                        {!! $item['icon']->toHtml() !!}
                    @elseif(!empty($item['icon']))
                        {{ $item['icon'] }}
                    @else
                        {{-- Icône par défaut : checkmark (timeline de succès) --}}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="{{ $itemIconClass }}">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </div>
            @endif

            {{-- Colonne end : titre et contenu, ou HTML personnalisé --}}
            @if($rendersEnd)
                <div class="{{ $endClasses }}">
                    @if($hasVisibleValue($endHtml))
                        {!! $endHtml !!}
                    @elseif($itemSide === 'both')
                        @if(!empty($item['title']))
                            <div class="text-lg font-black">{{ $item['title'] }}</div>
                        @endif
                        @if(isset($item['content']))
                            <div>{{ $item['content'] }}</div>
                        @endif
                    @else
                        {{ $endText ?? '' }}
                    @endif
                </div>
            @endif

            {{-- Séparateur après l'item (optionnel) --}}
            @if($hrAfter)
                <hr @if($afterClass) class="{{ $afterClass }}" @endif />
            @endif
        </li>
    @endforeach
</ul>
