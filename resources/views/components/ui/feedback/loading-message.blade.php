@props([
    'message' => __('common.loading'),
    'shape' => 'spinner',
    'size' => 'lg',
])

<div class="flex flex-col items-center gap-4">
    <x-daisy::ui.feedback.loading :shape="$shape" :size="$size" />
    
    @if($message)
        <p class="text-base text-base-content opacity-70">
            {{ $message }}
        </p>
    @endif
</div>

