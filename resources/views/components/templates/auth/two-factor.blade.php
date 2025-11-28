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

            <x-daisy::ui.feedback.alert color="info" class="shadow">
                {{ __('auth.two_factor_instructions') }}
            </x-daisy::ui.feedback.alert>

            <form action="{{ $action }}" method="POST" class="space-y-4">
                @csrf
                {{-- Two-factor code --}}
                <div class="space-y-2">
                    <label class="label">
                        <span class="label-text">{{ __('auth.two_factor_code') }}</span>
                        @if($errors->has('code'))
                            <span class="label-text-alt text-error">{{ $errors->first('code') }}</span>
                        @endif
                    </label>
                    <div data-module="otp-code" data-length="6" data-numeric-only="true" data-hidden-input-name="code" class="flex justify-center gap-3">
                        @for($i = 0; $i < 6; $i++)
                            <x-daisy::ui.inputs.input
                                type="text"
                                data-otp-digit
                                class="w-14 h-16 text-center text-3xl font-mono font-semibold {{ $errors->has('code') ? 'input-error' : '' }}"
                                aria-label="{{ __('auth.two_factor_code') }} {{ $i + 1 }}"
                            />
                        @endfor
                    </div>
                </div>

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

