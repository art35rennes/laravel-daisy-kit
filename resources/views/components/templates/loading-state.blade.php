@props([
    'type' => 'spinner', // spinner, skeleton, progress
    'message' => __('common.loading'),
    'size' => 'lg',
    'fullScreen' => false,
    'skeletonCount' => 3, // For skeleton type
    'theme' => null,
])

@php
    $containerClass = $fullScreen ? 'min-h-screen' : 'min-h-[calc(100vh-8rem)]';
@endphp

<x-daisy::layout.app :title="__('common.loading')" :theme="$theme" :container="true">
    <div class="{{ $containerClass }} flex items-center justify-center">
        <x-daisy::ui.errors.loading-state-content
            :type="$type"
            :message="$message"
            :size="$size"
            :skeletonCount="$skeletonCount"
        />
    </div>
</x-daisy::layout.app>

