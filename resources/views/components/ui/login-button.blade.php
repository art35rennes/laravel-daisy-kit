@props([
    'provider' => 'github', // github | google | apple | microsoft | facebook | twitter | discord | gitlab | linkedin | slack | steam | spotify | yahoo | wechat | metamask
    'label' => null,
    'size' => 'md',         // xs | sm | md | lg
    'variant' => 'solid',   // solid | outline | ghost | link
    'href' => '#',
])

@php
    $defaultLabels = [
        'google' => 'Se connecter avec Google',
        'apple' => 'Se connecter avec Apple',
        'microsoft' => 'Se connecter avec Microsoft',
        'github' => 'Se connecter avec GitHub',
        'twitter' => 'Se connecter avec Twitter',
        'facebook' => 'Se connecter avec Facebook',
        'discord' => 'Se connecter avec Discord',
        'gitlab' => 'Se connecter avec GitLab',
        'linkedin' => 'Se connecter avec LinkedIn',
        'slack' => 'Se connecter avec Slack',
        'steam' => 'Se connecter avec Steam',
        'spotify' => 'Se connecter avec Spotify',
        'yahoo' => 'Se connecter avec Yahoo',
        'wechat' => 'Se connecter avec WeChat',
        'metamask' => 'Se connecter avec MetaMask',
    ];
    $text = $label ?? ($defaultLabels[$provider] ?? 'Se connecter');

    // Classes par provider selon la doc DaisyUI (login buttons)
    $classMap = [
        'google' => 'bg-white text-black border-[#e5e5e5]',
        'apple' => 'bg-black text-white',
        'microsoft' => 'bg-white text-black border-[#e5e5e5]',
        'github' => 'btn-neutral',
        'twitter' => 'btn-info text-white',
        'facebook' => 'bg-[#1877F2] text-white',
        'discord' => 'bg-[#5865F2] text-white',
        'gitlab' => 'bg-[#FC6D26] text-white',
        'linkedin' => 'bg-[#0A66C2] text-white',
        'slack' => 'bg-white text-black border-[#e5e5e5]',
        'steam' => 'bg-[#171A21] text-white',
        'spotify' => 'bg-[#1DB954] text-white',
        'yahoo' => 'bg-[#6001D2] text-white',
        'wechat' => 'bg-[#07C160] text-white',
        'metamask' => 'bg-white text-black border-[#e5e5e5]',
    ];
    $btnClasses = $classMap[$provider] ?? 'btn-neutral';

    // Icônes Bootstrap Icons par provider
    $iconMap = [
        'google' => 'bi-google',
        'apple' => 'bi-apple',
        'microsoft' => 'bi-microsoft',
        'github' => 'bi-github',
        'twitter' => 'bi-twitter',
        'facebook' => 'bi-facebook',
        'discord' => 'bi-discord',
        'gitlab' => 'bi-box-arrow-in-right', // GitLab n'existe pas dans Bootstrap Icons
        'linkedin' => 'bi-linkedin',
        'slack' => 'bi-slack',
        'steam' => 'bi-steam',
        'spotify' => 'bi-spotify',
        'yahoo' => 'bi-box-arrow-in-right', // Yahoo n'existe pas dans Bootstrap Icons
        'wechat' => 'bi-wechat',
        'metamask' => 'bi-box-arrow-in-right', // MetaMask n'existe pas dans Bootstrap Icons
    ];
    $iconClass = $iconMap[$provider] ?? 'bi-box-arrow-in-right';
@endphp

<a href="{{ $href }}" class="inline-block">
    <x-daisy::ui.button :variant="$variant" color="" :size="$size" class="gap-2 {{ $btnClasses }}">
        <x-slot:icon>
            <x-icon :name="$iconClass" class="h-4 w-4" />
        </x-slot:icon>
        {{ $text }}
    </x-daisy::ui.button>
    <span class="sr-only">{{ $text }}</span>
    {{ $slot }}
</a>
