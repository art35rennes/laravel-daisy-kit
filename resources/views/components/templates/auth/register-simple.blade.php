@props([
    'title' => __('auth.register'),
    'theme' => null,
    // Form
    'action' => \Illuminate\Support\Facades\Route::has('register') ? route('register') : '#',
    'method' => 'POST',
    'loginUrl' => \Illuminate\Support\Facades\Route::has('login') ? route('login') : '#',
    // Validation
    'passwordConfirmation' => true,
    'termsUrl' => null,
    'privacyUrl' => null,
    'acceptTerms' => true,
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
                {{-- Name (optional) --}}
                <x-daisy::ui.partials.form-field name="name" :label="__('auth.name')" :required="false">
                    <x-daisy::ui.inputs.input
                        name="name"
                        type="text"
                        :value="old('name')"
                        autocomplete="name"
                        placeholder="{{ __('auth.name_placeholder') }}"
                        :class="$errors->has('name') ? 'input-error' : ''"
                    />
                </x-daisy::ui.partials.form-field>

                {{-- First name (optional) --}}
                <x-daisy::ui.partials.form-field name="first_name" :label="__('auth.first_name')" :required="false">
                    <x-daisy::ui.inputs.input
                        name="first_name"
                        type="text"
                        :value="old('first_name')"
                        autocomplete="given-name"
                        placeholder="{{ __('auth.first_name_placeholder') }}"
                        :class="$errors->has('first_name') ? 'input-error' : ''"
                    />
                </x-daisy::ui.partials.form-field>

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
                    <x-daisy::ui.inputs.input
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        placeholder="••••••••"
                        :class="$errors->has('password') ? 'input-error' : ''"
                    />
                </x-daisy::ui.partials.form-field>

                {{-- Password confirmation --}}
                @if($passwordConfirmation)
                    <x-daisy::ui.partials.form-field name="password_confirmation" :label="__('auth.password_confirmation')" :required="true">
                        <x-daisy::ui.inputs.input
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            placeholder="••••••••"
                            :class="$errors->has('password_confirmation') ? 'input-error' : ''"
                        />
                    </x-daisy::ui.partials.form-field>
                @endif

                {{-- Terms acceptance --}}
                @if($acceptTerms)
                    <div class="space-y-2">
                        <div class="flex items-start gap-3">
                            <x-daisy::ui.inputs.checkbox name="terms" id="terms" :checked="old('terms')" />
                            <label for="terms" class="text-sm cursor-pointer">
                                {{ __('auth.accept_terms') }}
                                @if($termsUrl)
                                    <a href="{{ $termsUrl }}" class="link link-hover" target="_blank">{{ __('auth.terms_link') }}</a>
                                @endif
                                @if($privacyUrl)
                                    {{ __('auth.and') }}
                                    <a href="{{ $privacyUrl }}" class="link link-hover" target="_blank">{{ __('auth.privacy_link') }}</a>
                                @endif
                            </label>
                        </div>
                        @if($errors->has('terms'))
                            <x-daisy::ui.advanced.validator state="error" :message="$errors->first('terms')" :full="false" as="div" />
                        @endif
                    </div>
                @endif

                <x-daisy::ui.inputs.button type="submit" variant="solid" class="w-full">
                    {{ __('auth.register') }}
                </x-daisy::ui.inputs.button>
            </form>

            <p class="text-center text-sm text-base-content/70">
                {{ __('auth.already_have_account') }}
                @if($loginUrl !== '#')
                    <a href="{{ $loginUrl }}" class="link link-hover">{{ __('auth.sign_in') }}</a>
                @else
                    <span class="opacity-70">{{ __('auth.sign_in') }}</span>
                @endif
            </p>
        </div>
    </div>
</x-daisy::layout.app>

