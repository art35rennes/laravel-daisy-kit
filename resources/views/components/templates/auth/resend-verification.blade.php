@props([
    'title' => __('auth.resend_verification'),
    'theme' => null,
    'description' => __('auth.resend_verification'),
    'action' => \Illuminate\Support\Facades\Route::has('verification.send') ? route('verification.send') : '#',
    'method' => 'POST',
    'submitButtonText' => __('auth.resend_verification'),
    'emailSent' => (bool) session('status'),
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="true">
    <x-daisy::ui.partials.theme-selector position="fixed" placement="top-right" />
    <div class="min-h-[calc(100vh-8rem)] flex items-center justify-center">
        <div class="w-full max-w-md space-y-6">
            @isset($logo)
                <div class="flex items-center justify-center">
                    {{ $logo }}
                </div>
            @endisset

            <div class="text-center space-y-1">
                <h1 class="text-2xl font-semibold">{{ __($title) }}</h1>
                <p class="text-base-content/70">{{ __($description) }}</p>
            </div>

            @if($emailSent)
                <x-daisy::ui.feedback.alert color="success" class="shadow-sm">
                    {{ session('status') }}
                </x-daisy::ui.feedback.alert>
            @endif

            <form action="{{ $action }}" method="POST" class="space-y-4">
                @csrf
                {{-- Optional email for unauthenticated flows --}}
                <x-daisy::ui.partials.form-field name="email" :label="__('auth.email')">
                        <x-daisy::ui.inputs.input
                            name="email"
                            type="email"
                            :value="old('email')"
                            autocomplete="email"
                            placeholder="email@example.com"
                            :class="$errors->has('email') ? 'input-error' : ''"
                        />
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.inputs.button type="submit" variant="solid" class="w-full">
                    {{ __($submitButtonText) }}
                </x-daisy::ui.inputs.button>
            </form>
        </div>
    </div>
</x-daisy::layout.app>


