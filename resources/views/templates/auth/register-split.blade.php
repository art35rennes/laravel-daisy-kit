@props([
    'title' => __('auth.register'),
    'theme' => null,
    // Form (identique à register-simple)
    'action' => \Illuminate\Support\Facades\Route::has('register') ? route('register') : '#',
    'method' => 'POST',
    'loginUrl' => \Illuminate\Support\Facades\Route::has('login') ? route('login') : '#',
    'passwordConfirmation' => true,
    'termsUrl' => null,
    'privacyUrl' => null,
    'acceptTerms' => true,
    // UI
    'backgroundImage' => null,
    'showTestimonial' => false,
    'testimonial' => null, // ['quote' => '', 'author' => '', 'role' => '', 'avatar' => '', 'rating' => 5]
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false">
    <x-daisy::ui.partials.theme-selector position="fixed" placement="top-right" />
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
        {{-- Left side: form --}}
        <div class="flex items-center justify-center p-6 md:p-10">
            <div class="w-full max-w-md space-y-6">
                {{-- Brand / logo --}}
                @isset($logo)
                    <div class="flex items-center gap-2">
                        {{ $logo }}
                    </div>
                @endisset

                <div>
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

                <p class="text-sm text-base-content/70">
                    {{ __('auth.already_have_account') }}
                    @if($loginUrl !== '#')
                        <a href="{{ $loginUrl }}" class="link link-hover">{{ __('auth.sign_in') }}</a>
                    @else
                        <span class="opacity-70">{{ __('auth.sign_in') }}</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Right side: background + testimonial --}}
        <div class="hidden lg:block">
            <x-daisy::ui.layout.hero :imageUrl="$backgroundImage" :overlay="true" fullScreen="true" class="!min-h-screen">
                @if($showTestimonial && is_array($testimonial))
                    <div class="max-w-xl">
                        @php
                            $rating = (int) ($testimonial['rating'] ?? 5);
                            $quote = (string) ($testimonial['quote'] ?? '');
                            $author = (string) ($testimonial['author'] ?? '');
                            $role = (string) ($testimonial['role'] ?? '');
                            $avatar = (string) ($testimonial['avatar'] ?? '');
                        @endphp
                        <div class="mb-3">
                            <div class="rating">
                                @for($i=0; $i<$rating; $i++)
                                    <input type="radio" class="mask mask-star bg-warning" checked aria-hidden="true" />
                                @endfor
                            </div>
                        </div>
                        <blockquote class="text-2xl leading-snug">
                            {{ $quote }}
                        </blockquote>
                        <div class="mt-6 flex items-center gap-3">
                            @if($avatar)
                                <div class="avatar">
                                    <div class="w-10 rounded-full">
                                        <img src="{{ $avatar }}" alt="{{ $author }}" />
                                    </div>
                                </div>
                            @endif
                            <div>
                                <div class="font-medium">{{ $author }}</div>
                                @if($role)
                                    <div class="text-sm opacity-80">{{ $role }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </x-daisy::ui.layout.hero>
        </div>
    </div>
</x-daisy::layout.app>

