@props([
    'title' => __('auth.verify_email'),
    'theme' => null,
    'message' => __('auth.verification_sent'),
    'resendUrl' => \Illuminate\Support\Facades\Route::has('verification.send') ? route('verification.send') : '#',
    'logoutUrl' => \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : '#',
    'showLogout' => true,
    'resendButtonText' => __('auth.resend_verification'),
    'logoutText' => __('auth.logout'),
])

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

            <div class="flex items-center justify-center">
                <form action="{{ $resendUrl }}" method="POST">
                    @csrf
                    <x-daisy::ui.inputs.button type="submit" variant="solid">
                        @lang($resendButtonText)
                    </x-daisy::ui.inputs.button>
                </form>
            </div>

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


