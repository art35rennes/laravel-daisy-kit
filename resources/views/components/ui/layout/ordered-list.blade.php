@props([
    'items' => [],
    'sortable' => false,
    'handle' => true,
    'name' => null,
    'persist' => false,
    'disabled' => false,
    'module' => null,
])

@php
    $rootId = $attributes->get('id') ?: ('ordered-list-'.uniqid());
    $resolvedSortable = (bool) $sortable && ! (bool) $disabled;
    $resolvedPersist = (bool) $persist && filled($name);
    $renderedItems = is_array($items) ? array_values($items) : [];
    $hasSlotContent = isset($slot) && trim((string) $slot) !== '';
    $rootClasses = trim('list list-decimal daisy-ordered-list '.($attributes->get('class') ?? ''));
    $attributes = $attributes->except('class');
@endphp

<ol
    {{ $attributes->merge(['id' => $rootId, 'class' => $rootClasses]) }}
    data-module="{{ $module ?? 'ordered-list' }}"
    data-ordered-list="1"
    data-sortable="{{ $resolvedSortable ? 'true' : 'false' }}"
    data-disabled="{{ $disabled ? 'true' : 'false' }}"
    data-persist="{{ $resolvedPersist ? 'true' : 'false' }}"
>
    @if($hasSlotContent)
        {{ $slot }}
    @else
        @foreach($renderedItems as $index => $item)
            @php
                $itemId = filled($item['id'] ?? null) ? (string) $item['id'] : 'ordered-item-'.$index;
                $itemDisabled = (bool) ($item['disabled'] ?? false);
                $itemLabel = $item['label'] ?? $item['title'] ?? $itemId;
                $itemContent = $item['content'] ?? null;
            @endphp
            <li
                class="list-row daisy-ordered-list-item{{ $itemDisabled ? ' opacity-60' : '' }}"
                data-ordered-list-item
                data-id="{{ $itemId }}"
                data-disabled="{{ $itemDisabled ? 'true' : 'false' }}"
            >
                <div class="flex items-start gap-3">
                    @if($handle && $resolvedSortable)
                        <button
                            type="button"
                            class="btn btn-ghost btn-xs btn-square mt-0.5 cursor-grab daisy-drag-handle"
                            data-ordered-list-handle
                            aria-label="Reorder {{ $itemLabel }}"
                            @disabled($itemDisabled || ! $resolvedSortable)
                        >
                            <span aria-hidden="true">⋮⋮</span>
                        </button>
                    @endif

                    <div class="min-w-0 flex-1">
                        @if(filled($itemLabel))
                            <div class="font-medium">{{ $itemLabel }}</div>
                        @endif

                        @if($itemContent instanceof \Illuminate\Contracts\Support\Htmlable)
                            {!! $itemContent->toHtml() !!}
                        @elseif($itemContent instanceof \Illuminate\Support\HtmlString)
                            {!! $itemContent !!}
                        @elseif(filled($itemContent))
                            <div class="text-sm text-base-content/70">{{ $itemContent }}</div>
                        @endif
                    </div>
                </div>
            </li>
        @endforeach
    @endif

</ol>

@if($resolvedPersist)
    <input type="hidden" name="{{ $name }}" value="" data-ordered-list-input-for="{{ $rootId }}">
@endif

@include('daisy::components.partials.assets')
