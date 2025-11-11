@props([
    'action' => '#',
    'method' => 'GET',
    'inline' => true,
    'compact' => true,
    'showLabels' => false,
    'submitText' => __('form.search'),
    'resetText' => __('form.reset'),
    'showReset' => true,
    'size' => 'sm',
])

@php
    $sizeMap = [
        'xs' => 'xs',
        'sm' => 'sm',
        'md' => 'md',
        'lg' => 'lg',
        'xl' => 'xl',
    ];
    $inputSize = $sizeMap[$size] ?? 'sm';
@endphp

<form action="{{ $action }}" method="{{ $method }}" class="w-full">
    @if($method !== 'GET')
        @csrf
    @endif

    <div class="flex flex-col sm:flex-row gap-2 items-start sm:items-end">
        @isset($slot)
            @if(trim((string) $slot) !== '')
                {{ $slot }}
            @endif
        @endisset

        <div class="flex gap-2 shrink-0">
            <x-daisy::ui.inputs.button type="submit" :size="$inputSize">
                {{ $submitText }}
            </x-daisy::ui.inputs.button>

            @if($showReset)
                <x-daisy::ui.inputs.button type="reset" variant="ghost" :size="$inputSize">
                    {{ $resetText }}
                </x-daisy::ui.inputs.button>
            @endif

            @isset($actions)
                {{ $actions }}
            @endisset
        </div>
    </div>
</form>

