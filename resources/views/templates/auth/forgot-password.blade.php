@props([
    'title' => __('auth.forgot_password'),
    'theme' => null,
    // Form
    'action' => \Illuminate\Support\Facades\Route::has('password.email') ? route('password.email') : '#',
    'method' => 'POST',
    'loginUrl' => \Illuminate\Support\Facades\Route::has('login') ? route('login') : '#',
    // Messages
    'status' => session('status'), // Laravel password reset status
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="true">
    <x-daisy::ui.partials.theme-selector position="fixed" placement="top-right" />
    <div class="min-h-[calc(100vh-8rem)] flex items-center justify-center">
        <div class="w-full max-w-md space-y-6">
            {{-- Brand / logo --}}
            @isset($logo)
                <div class="flex items-center justify-center">
                    {{ $logo }}
                </div>
            @endisset

            <div class="text-center space-y-1">
                <h1 class="text-2xl font-semibold">{{ __($title) }}</h1>
                <p class="text-base-content/70">{{ __('auth.forgot_password_description') }}</p>
            </div>

            @if($status)
                <x-daisy::ui.feedback.alert color="success" class="shadow-sm">
                    {{ $status }}
                </x-daisy::ui.feedback.alert>
            @endif

            <form action="{{ $action }}" method="POST" class="space-y-4">
                @csrf
                {{-- Email --}}
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
                    {{ __('auth.send_reset_link') }}
                </x-daisy::ui.inputs.button>
            </form>

            @if($loginUrl !== '#')
                <p class="text-center text-sm text-base-content/70">
                    {{ __('auth.remember_password') }}
                    <a href="{{ $loginUrl }}" class="link link-hover">{{ __('auth.sign_in') }}</a>
                </p>
            @endif
        </div>
    </div>
</x-daisy::layout.app>

