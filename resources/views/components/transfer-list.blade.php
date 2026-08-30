@props([
    'name' => null,
    'label' => 'Transfer list',
    'items' => [],
    'value' => [],
    'sourceLabel' => 'Available',
    'targetLabel' => 'Selected',
    'searchable' => true,
    'maxItems' => null,
    'disabled' => false,
    'required' => false,
    'sortable' => true,
])

<section {{ $attributes->class(['daisy-kit-transfer-list']) }} data-daisy-kit-module="transfer-list">
    <p class="alert alert-error" data-daisy-kit-status hidden role="alert"></p>
    <p class="label-text">{{ $label }}</p>
    <div class="daisy-kit-transfer-list__columns" data-daisy-kit-transfer-content>
        <div>
            <label class="label"><span class="label-text">{{ $sourceLabel }}</span></label>
            @if ($searchable)<input class="input input-bordered w-full" data-daisy-kit-transfer-search="source" type="search" placeholder="Search {{ $sourceLabel }}">@endif
            <ul class="menu rounded-box border border-base-300 bg-base-100" aria-label="{{ $sourceLabel }}" data-daisy-kit-transfer-source role="listbox" aria-multiselectable="true"></ul>
        </div>
        <div class="daisy-kit-transfer-list__actions" aria-label="Transfer controls">
            <button class="btn btn-sm" data-daisy-kit-transfer-move="to-target" type="button" @disabled($disabled)>Add →</button>
            <button class="btn btn-sm" data-daisy-kit-transfer-move="to-source" type="button" @disabled($disabled)>← Remove</button>
        </div>
        <div>
            <label class="label"><span class="label-text">{{ $targetLabel }}</span></label>
            @if ($searchable)<input class="input input-bordered w-full" data-daisy-kit-transfer-search="target" type="search" placeholder="Search {{ $targetLabel }}">@endif
            <ul class="menu rounded-box border border-base-300 bg-base-100" aria-label="{{ $targetLabel }}" data-daisy-kit-transfer-target role="listbox" aria-multiselectable="true"></ul>
        </div>
    </div>
    <div data-daisy-kit-transfer-values></div>
    @if ($required)
        <input class="sr-only" data-daisy-kit-transfer-required type="text" tabindex="-1" aria-label="{{ $label }} selection" required @disabled($disabled)>
    @endif
    <script data-daisy-kit-config type="application/json">{!! \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'name' => $name,
        'items' => $items,
        'value' => $value,
        'searchable' => $searchable === true,
        'maxItems' => $maxItems,
        'disabled' => $disabled === true,
        'required' => $required === true,
        'sortable' => $sortable === true,
    ]) !!}</script>
</section>
