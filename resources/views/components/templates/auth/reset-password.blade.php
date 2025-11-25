@props([
    'title' => __('auth.reset_password'),
    'theme' => null,
    'description' => __('auth.send_reset_link'),
    'action' => \Illuminate\Support\Facades\Route::has('password.email') ? route('password.email') : '#',
    'method' => 'POST',
    'backToLoginUrl' => \Illuminate\Support\Facades\Route::has('login') ? route('login') : '#',
    'backToLoginText' => __('auth.back_to_login'),
    'submitButtonText' => __('auth.send_reset_link'),
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
                <x-daisy::ui.partials.form-field name="email" :label="__('auth.email')" :required="true">
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

            @if($backToLoginUrl !== '#')
                <p class="text-center text-sm">
                    <a href="{{ $backToLoginUrl }}" class="link link-hover">{{ __($backToLoginText) }}</a>
                </p>
            @endif
        </div>
    </div>
</x-daisy::layout.app>


