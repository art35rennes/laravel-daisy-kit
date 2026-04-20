@props([
    'editable' => true,
    'items' => [],
    'columns' => 12,
    'cellHeight' => 96,
    'gap' => 16,
    'static' => false,
    'float' => false,
    'minRow' => 0,
    'acceptWidgets' => false,
    'layout' => 'list',
    'responsive' => null,
])

@php
    $rootId = $attributes->get('id') ?: ('editable-grid-'.uniqid());
    $resolvedEditable = (bool) $editable;
    $resolvedStatic = (bool) $static || ! $resolvedEditable;
    $resolvedColumns = max(1, (int) $columns);
    $resolvedCellHeight = max(40, (int) $cellHeight);
    $resolvedGap = max(0, (int) $gap);
    $resolvedFloat = (bool) $float;
    $resolvedMinRow = max(0, (int) $minRow);
    $resolvedLayout = in_array($layout, ['list', 'compact', 'moveScale', 'move', 'scale', 'none'], true) ? $layout : 'list';
    $resolvedAcceptWidgets = is_bool($acceptWidgets) ? $acceptWidgets : (filled($acceptWidgets) ? (string) $acceptWidgets : false);
    $resolvedResponsive = null;

    if (is_bool($responsive)) {
        $resolvedResponsive = $responsive ? [
            'columnWidth' => 320,
            'columnMax' => $resolvedColumns,
            'layout' => $resolvedLayout,
        ] : null;
    } elseif (is_array($responsive) && $responsive !== []) {
        $resolvedResponsive = $responsive;
        if (! array_key_exists('layout', $resolvedResponsive)) {
            $resolvedResponsive['layout'] = $resolvedLayout;
        }
    }

    $renderedItems = is_array($items) ? array_values($items) : [];
    $hasSlotContent = isset($slot) && trim((string) $slot) !== '';

    $config = [
        'editable' => $resolvedEditable,
        'columns' => $resolvedColumns,
        'cellHeight' => $resolvedCellHeight,
        'gap' => $resolvedGap,
        'static' => $resolvedStatic,
        'float' => $resolvedFloat,
        'minRow' => $resolvedMinRow,
        'acceptWidgets' => $resolvedAcceptWidgets,
        'layout' => $resolvedLayout,
        'responsive' => $resolvedResponsive,
    ];

    $rootClasses = trim('grid-stack daisy-editable-grid '.($attributes->get('class') ?? ''));
    $attributes = $attributes->except('class');
@endphp

<div
    {{ $attributes->merge(['id' => $rootId, 'class' => $rootClasses]) }}
    data-module="editable-grid"
    data-editable-grid="1"
    data-editable="{{ $resolvedEditable ? '1' : '0' }}"
    data-static="{{ $resolvedStatic ? '1' : '0' }}"
>
    @if($hasSlotContent)
        {{ $slot }}
    @else
        @foreach($renderedItems as $item)
            <x-daisy::ui.layout.editable-grid-item
                :id="$item['id'] ?? null"
                :type="$item['type'] ?? null"
                :x="$item['x'] ?? 0"
                :y="$item['y'] ?? 0"
                :w="$item['w'] ?? 3"
                :h="$item['h'] ?? 2"
                :meta="$item['meta'] ?? null"
            >
                @php($content = $item['content'] ?? null)
                @if($content instanceof \Illuminate\Contracts\Support\Htmlable)
                    {!! $content->toHtml() !!}
                @elseif($content instanceof \Illuminate\Support\HtmlString)
                    {!! $content !!}
                @elseif(filled($content))
                    {{ $content }}
                @endif
            </x-daisy::ui.layout.editable-grid-item>
        @endforeach
    @endif

    <script type="application/json" data-editable-grid-config>
        {!! json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>

    @include('daisy::components.partials.assets')
</div>
