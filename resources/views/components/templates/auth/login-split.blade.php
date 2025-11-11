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
                    {{-- Email --}}
                    <x-daisy::ui.partials.form-field name="email" :label="__('auth.email')" :required="true">
                        <x-slot:labelSlot>{{ __('auth.email') }}</x-slot:labelSlot>
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

                <p class="text-sm text-base-content/70">
                    {{ __('auth.first_time') }}
                    @if($signupUrl !== '#')
                        <a href="{{ $signupUrl }}" class="link link-hover">{{ __('auth.signup_for_free') }}</a>
                    @else
                        <span class="opacity-70">{{ __('auth.signup_for_free') }}</span>
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


