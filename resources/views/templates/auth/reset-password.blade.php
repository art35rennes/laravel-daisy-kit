@props([
    'title' => __('daisy::auth.reset_password'),
    'theme' => null,
    'description' => __('daisy::auth.reset_password_description'),
    'action' => \Illuminate\Support\Facades\Route::has('password.store') ? route('password.store') : '#',
    'method' => 'POST',
    'token' => request()->route('token'),
    'email' => old('email', request('email')),
    'backToLoginUrl' => \Illuminate\Support\Facades\Route::has('login') ? route('login') : '#',
    'backToLoginText' => __('daisy::auth.back_to_login'),
    'passwordConfirmation' => true,
])

@php
    $formMethod = strtoupper($method);
    $htmlMethod = $formMethod === 'GET' ? 'GET' : 'POST';
@endphp

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

            <form action="{{ $action }}" method="{{ $htmlMethod }}" class="space-y-4">
                @if($htmlMethod !== 'GET')
                    @csrf
                @endif

                @if(! in_array($formMethod, ['GET', 'POST'], true))
                    @method($formMethod)
                @endif

                @if($token)
                    <input type="hidden" name="token" value="{{ $token }}">
                @endif

                <x-daisy::ui.partials.form-field name="email" :label="__('daisy::auth.email')" :required="true">
                    <x-daisy::ui.inputs.input
                        name="email"
                        type="email"
                        :value="$email"
                        autocomplete="username"
                        placeholder="email@example.com"
                        :class="$errors->has('email') ? 'input-error' : ''"
                    />
                </x-daisy::ui.partials.form-field>

                <x-daisy::ui.partials.form-field name="password" :label="__('daisy::auth.password')" :required="true">
                    <x-daisy::ui.inputs.input
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        :class="$errors->has('password') ? 'input-error' : ''"
                    />
                </x-daisy::ui.partials.form-field>

                @if($passwordConfirmation)
                    <x-daisy::ui.partials.form-field name="password_confirmation" :label="__('daisy::auth.password_confirmation')" :required="true">
                        <x-daisy::ui.inputs.input
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            :class="$errors->has('password_confirmation') ? 'input-error' : ''"
                        />
                    </x-daisy::ui.partials.form-field>
                @endif

                <x-daisy::ui.inputs.button type="submit" variant="solid" class="w-full">
                    {{ __('daisy::auth.reset_password') }}
                </x-daisy::ui.inputs.button>
            </form>

            @if($backToLoginUrl !== '#')
                <p class="text-center text-sm text-base-content/70">
                    <a href="{{ $backToLoginUrl }}" class="link link-hover">{{ __($backToLoginText) }}</a>
                </p>
            @endif
        </div>
    </div>
</x-daisy::layout.app>
