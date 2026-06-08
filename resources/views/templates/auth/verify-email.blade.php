@props([
    'title' => __('daisy::auth.verify_email'),
    'theme' => null,
    'message' => __('daisy::auth.verification_sent'),
    'resendUrl' => \Illuminate\Support\Facades\Route::has('verification.send') ? route('verification.send') : '#',
    'logoutUrl' => \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : '#',
    'showResend' => null,
    'showLogout' => true,
    'resendButtonText' => __('daisy::auth.resend_verification'),
    'logoutText' => __('daisy::auth.logout'),
])

@php
    $normalizeUrl = function($url, $fallback = '#') {
        if (!is_string($url) && !$url instanceof \Stringable) {
            return $fallback;
        }

        $url = trim((string) $url);

        if ($url === '') {
            return $fallback;
        }

        if ($url === '#' || str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url;
        }

        return preg_match('/^https?:\/\//i', $url) === 1 ? $url : $fallback;
    };

    $resendUrl = $normalizeUrl($resendUrl);
    $logoutUrl = $normalizeUrl($logoutUrl);
    $shouldShowResend = $showResend ?? $resendUrl !== '#';
@endphp

<x-daisy::layout.app :title="$title" :theme="$theme" :container="true">
    <x-daisy::ui.partials.theme-selector position="fixed" placement="top-right" />
    <div class="min-h-[calc(100vh-8rem)] flex items-center justify-center">
        <div class="w-full max-w-md text-center space-y-6">
            @isset($logo)
                <div class="flex items-center justify-center">
                    {{ $logo }}
                </div>
            @endisset

            <h1 class="text-2xl font-semibold">@lang($title)</h1>
            <p class="text-base-content/80">@lang($message)</p>

            @if($shouldShowResend)
                <div class="flex items-center justify-center">
                    <form action="{{ $resendUrl }}" method="POST">
                        @csrf
                        <x-daisy::ui.inputs.button type="submit" variant="solid">
                            @lang($resendButtonText)
                        </x-daisy::ui.inputs.button>
                    </form>
                </div>
            @endif

            @if($showLogout && $logoutUrl !== '#')
                <form action="{{ $logoutUrl }}" method="POST" class="pt-2">
                    @csrf
                    <x-daisy::ui.inputs.button type="submit" variant="link">
                        @lang($logoutText)
                    </x-daisy::ui.inputs.button>
                </form>
            @endif
        </div>
    </div>
</x-daisy::layout.app>
