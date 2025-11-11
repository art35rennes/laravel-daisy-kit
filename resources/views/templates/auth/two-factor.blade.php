@props([
    'title' => __('auth.two_factor'),
    'theme' => null,
    // Form
    'action' => \Illuminate\Support\Facades\Route::has('two-factor.login') ? route('two-factor.login') : '#',
    'method' => 'POST',
    'recoveryUrl' => \Illuminate\Support\Facades\Route::has('two-factor.recovery') ? route('two-factor.recovery') : '#',
    'logoutUrl' => \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : '#',
    // Options
    'showRecovery' => true,
    'showLogout' => true,
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
                <p class="text-base-content/70">{{ __('auth.two_factor_description') }}</p>
            </div>

            <x-daisy::ui.feedback.alert color="info" class="shadow-sm">
                {{ __('auth.two_factor_instructions') }}
            </x-daisy::ui.feedback.alert>

            <form action="{{ $action }}" method="POST" class="space-y-4">
                @csrf
                {{-- Two-factor code --}}
                <x-daisy::ui.partials.form-field name="code" :label="__('auth.two_factor_code')" :required="true">
                    <x-daisy::ui.inputs.input
                        name="code"
                        type="text"
                        pattern="[0-9]{6}"
                        inputmode="numeric"
                        maxlength="6"
                        autocomplete="one-time-code"
                        placeholder="000000"
                        class="text-center text-2xl tracking-widest font-mono"
                        :class="$errors->has('code') ? 'input-error' : ''"
                    />
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.inputs.button type="submit" variant="solid" class="w-full">
                    {{ __('auth.verify') }}
                </x-daisy::ui.inputs.button>
            </form>

            <div class="flex flex-col gap-2">
                @if($showRecovery && $recoveryUrl !== '#')
                    <p class="text-center text-sm">
                        <a href="{{ $recoveryUrl }}" class="link link-hover">{{ __('auth.two_factor_recovery') }}</a>
                    </p>
                @endif

                @if($showLogout && $logoutUrl !== '#')
                    <form action="{{ $logoutUrl }}" method="POST" class="text-center">
                        @csrf
                        <button type="submit" class="link link-hover text-sm">
                            {{ __('auth.two_factor_logout') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-daisy::layout.app>

