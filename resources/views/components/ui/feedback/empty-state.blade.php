@props([
    'icon' => 'bi-inbox', // Blade icon name
    'title' => null,
    'message' => null,
    'actionLabel' => null,
    'actionUrl' => null,
    'size' => 'md', // sm, md, lg
    'preset' => null, // no-results | no-data | no-permission
    'iconName' => null,
])

@php
    $presetMap = [
        'no-results' => ['icon' => 'bi-search', 'title' => __('daisy::common.no_results')],
        'no-data' => ['icon' => 'bi-inbox', 'title' => __('daisy::common.empty')],
        'no-permission' => ['icon' => 'bi-lock', 'title' => __('daisy::common.access_unavailable')],
    ];
    $presetConfig = is_string($preset) && isset($presetMap[$preset]) ? $presetMap[$preset] : [];
    $resolvedIcon = $iconName ?: ($presetConfig['icon'] ?? $icon);
    $resolvedTitle = $title ?: ($presetConfig['title'] ?? __('daisy::common.empty'));

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
    @if($resolvedIcon)
        <div class="{{ $iconSizeMap[$size] ?? 'w-16 h-16' }} text-base-content opacity-50">
            <x-icon :name="$resolvedIcon" class="w-full h-full" />
        </div>
    @endif
    
    @if($resolvedTitle)
        <h3 class="{{ $titleSizeMap[$size] ?? 'text-xl' }} font-semibold text-base-content">
            {{ $resolvedTitle }}
        </h3>
    @endif
    
    @if($message)
        <p class="{{ $sizeMap[$size] ?? 'text-base' }} text-base-content opacity-70 text-center max-w-md">
            {{ $message }}
        </p>
    @endif
    
    @isset($actions)
        <div class="mt-2 flex flex-wrap items-center justify-center gap-2">
            {{ $actions }}
        </div>
    @elseif($actionLabel && $actionUrl)
        <div class="mt-2">
            <x-daisy::ui.inputs.button
                tag="a"
                :href="$actionUrl"
                size="sm"
                color="primary"
            >
                {{ $actionLabel }}
            </x-daisy::ui.inputs.button>
        </div>
    @endif
</div>
