@props([
    'title' => __('auth.welcome'),
    'theme' => null,
    // Form
    'action' => \Illuminate\Support\Facades\Route::has('login') ? route('login') : '#',
    'method' => 'POST',
    'rememberMe' => true,
    'rememberMeDays' => 30,
    'forgotPasswordUrl' => \Illuminate\Support\Facades\Route::has('password.request') ? route('password.request') : '#',
    'signupUrl' => \Illuminate\Support\Facades\Route::has('register') ? route('register') : '#',
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

            <div class="text-center">
                <h1 class="text-2xl font-semibold">{{ __($title) }}</h1>
            </div>

            {{-- Social login (slot) --}}
            @if(trim((string) ($socialLogin ?? '')) !== '')
                <div class="flex flex-col sm:flex-row gap-3">
                    {{ $socialLogin }}
                </div>
                <div class="divider my-6">{{ __('auth.or') }}</div>
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

                {{-- Password --}}
                <x-daisy::ui.partials.form-field name="password" :label="__('auth.password')" :required="true">
                    <x-slot:labelSlot>
                        <div class="w-full flex items-center justify-between">
                            <span>{{ __('auth.password') }}</span>
                            @if($forgotPasswordUrl !== '#')
                                <a href="{{ $forgotPasswordUrl }}" class="link link-hover text-sm">{{ __('auth.forgot_password') }}</a>
                            @endif
                        </div>
                    </x-slot:labelSlot>
                        <x-daisy::ui.inputs.input
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            :class="$errors->has('password') ? 'input-error' : ''"
                        />
                </x-daisy::ui.partials.form-field>

                @if($rememberMe)
                    <div class="flex items-center gap-3">
                        <x-daisy::ui.inputs.checkbox name="remember" />
                        <span class="text-sm">{{ __('auth.remember_me', ['days' => $rememberMeDays]) }}</span>
                    </div>
                @endif

                <x-daisy::ui.inputs.button type="submit" variant="solid" class="w-full">
                    {{ __('auth.login') }}
                </x-daisy::ui.inputs.button>
            </form>

            <p class="text-center text-sm text-base-content/70">
                {{ __('auth.first_time') }}
                @if($signupUrl !== '#')
                    <a href="{{ $signupUrl }}" class="link link-hover">{{ __('auth.signup_for_free') }}</a>
                @else
                    <span class="opacity-70">{{ __('auth.signup_for_free') }}</span>
                @endif
            </p>
        </div>
    </div>
</x-daisy::layout.app>


