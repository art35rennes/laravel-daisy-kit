@props([
    'icon' => 'bi-inbox', // Blade icon name
    'title' => __('common.empty'),
    'message' => null,
    'actionLabel' => null,
    'actionUrl' => null,
    'size' => 'md', // sm, md, lg
])

@php
    $sizeMap = [
        'xs' => 'text-xs',
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
    ];
    $iconSizeMap = [
        'xs' => 'w-10 h-10',
        'sm' => 'w-12 h-12',
        'md' => 'w-16 h-16',
        'lg' => 'w-24 h-24',
    ];
    $titleSizeMap = [
        'xs' => 'text-base',
        'sm' => 'text-lg',
        'md' => 'text-xl',
        'lg' => 'text-2xl',
    ];
    $containerSizeMap = [
        'xs' => 'gap-3 py-6 px-4',
        'sm' => 'gap-4 py-10 px-4',
        'md' => 'gap-5 py-12 px-6',
        'lg' => 'gap-6 py-14 px-8',
    ];
    $containerClass = $containerSizeMap[$size] ?? $containerSizeMap['md'];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center '.$containerClass]) }}>
    @if($icon)
        <div class="{{ $iconSizeMap[$size] ?? 'w-16 h-16' }} text-base-content opacity-50">
            <x-icon :name="$icon" class="w-full h-full" />
        </div>
    @endif
    
    @if($title)
        <h3 class="{{ $titleSizeMap[$size] ?? 'text-xl' }} font-semibold text-base-content">
            {{ $title }}
        </h3>
    @endif
    
    @if($message)
        <p class="{{ $sizeMap[$size] ?? 'text-base' }} text-base-content opacity-70 text-center max-w-md">
            {{ $message }}
        </p>
    @endif
    
    @if($actionLabel && $actionUrl)
        <x-daisy::ui.inputs.button
            tag="a"
            :href="$actionUrl"
            size="sm"
            color="primary"
            class="mt-2"
        >
            {{ $actionLabel }}
        </x-daisy::ui.inputs.button>
    @endif
</div>

