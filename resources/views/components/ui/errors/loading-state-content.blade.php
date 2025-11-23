@props([
    'type' => 'spinner', // spinner, skeleton, progress
    'message' => __('common.loading'),
    'size' => 'lg',
    'skeletonCount' => 3,
])

@if($type === 'spinner')
    <x-daisy::ui.feedback.loading-message
        :message="$message"
        shape="spinner"
        :size="$size"
    />
@elseif($type === 'skeleton')
    <div class="flex flex-col items-center gap-6 w-full">
        @if($message)
            <p class="text-base text-base-content opacity-70">
                {{ $message }}
            </p>
        @endif
        
        <div class="w-full space-y-4">
            @for($i = 0; $i < $skeletonCount; $i++)
                <x-daisy::ui.feedback.skeleton
                    width="w-full"
                    height="h-20"
                    rounded="md"
                />
            @endfor
        </div>
    </div>
@elseif($type === 'progress')
    <div class="flex flex-col items-center gap-4 w-full">
        @if($message)
            <p class="text-base text-base-content opacity-70">
                {{ $message }}
            </p>
        @endif
        
        <x-daisy::ui.data-display.progress
            color="primary"
            class="w-full max-w-md"
        />
    </div>
@endif

