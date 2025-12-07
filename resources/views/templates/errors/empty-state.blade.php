@props([
    'icon' => 'bi-inbox',
    'title' => __('common.empty'),
    'message' => null,
    'actionLabel' => null,
    'actionUrl' => null,
    'actionVariant' => 'primary',
    'size' => 'md',
    'illustration' => null, // Custom illustration image
    'theme' => null,
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="true">
    <div class="min-h-[calc(100vh-8rem)] flex items-center justify-center">
        <x-daisy::ui.layout.card class="max-w-md w-full">
            @if($illustration)
                <x-slot:figure>
                    <img src="{{ $illustration }}" alt="" class="w-full h-auto" />
                </x-slot:figure>
            @endif
            
            <div class="card-body">
                <x-daisy::ui.feedback.empty-state
                    :icon="$icon"
                    :title="$title"
                    :message="$message"
                    :actionLabel="$actionLabel"
                    :actionUrl="$actionUrl"
                    :size="$size"
                />
            </div>
        </x-daisy::ui.layout.card>
    </div>
</x-daisy::layout.app>


