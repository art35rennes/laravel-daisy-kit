@props([
    'homeUrl' => Route::has('home') ? route('home') : '/',
    'backUrl' => url()->previous(),
    'showBack' => true,
    'showHome' => true,
])

@if($showBack || $showHome)
    <div class="flex flex-wrap items-center justify-center gap-3">
        @if($showBack)
            <x-daisy::ui.inputs.button
                tag="a"
                :href="$backUrl"
                variant="outline"
                color="neutral"
            >
                <x-slot:icon>
                    <x-icon name="bi-arrow-left" class="w-4 h-4" />
                </x-slot:icon>
                {{ __('errors.go_back') }}
            </x-daisy::ui.inputs.button>
        @endif
        
        @if($showHome)
            <x-daisy::ui.inputs.button
                tag="a"
                :href="$homeUrl"
                color="primary"
            >
                <x-slot:icon>
                    <x-icon name="bi-house" class="w-4 h-4" />
                </x-slot:icon>
                {{ __('errors.go_home') }}
            </x-daisy::ui.inputs.button>
        @endif
    </div>
@endif

