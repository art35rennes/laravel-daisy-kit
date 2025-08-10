@props([
    'name' => null,
    'size' => 'sm', // xs | sm | md | lg
    'class' => '',
    'ariaLabel' => null,
])

@php
    $sizeMap = [
        'xs' => 'h-3 w-auto',
        'sm' => 'h-4 w-auto',
        'md' => 'h-5 w-auto',
        'lg' => 'h-6 w-auto',
    ];
    $sizeClass = $sizeMap[$size] ?? 'h-4 w-auto';
    $base = trim('inline-block align-middle '.$sizeClass.' '.$class);
@endphp

@switch($name)
    @case('arrow-right')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'aria-label' => $ariaLabel]) }}>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
        </svg>
        @break

    @case('external-link')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'aria-label' => $ariaLabel]) }}>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7v10a2 2 0 002 2h10" />
        </svg>
        @break

    @case('github')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 24 24', 'fill' => 'currentColor', 'aria-label' => $ariaLabel ?? 'GitHub']) }}>
            <path d="M12 .5a12 12 0 00-3.79 23.4c.6.11.82-.26.82-.58v-2.03c-3.34.73-4.04-1.61-4.04-1.61-.55-1.4-1.34-1.77-1.34-1.77-1.09-.75.08-.74.08-.74 1.2.09 1.83 1.24 1.83 1.24 1.07 1.84 2.8 1.31 3.48 1 .11-.78.42-1.31.76-1.61-2.67-.3-5.47-1.34-5.47-5.97 0-1.32.47-2.39 1.24-3.23-.12-.3-.54-1.52.12-3.17 0 0 1.01-.32 3.3 1.23a11.5 11.5 0 016 0c2.3-1.55 3.3-1.23 3.3-1.23.66 1.65.24 2.87.12 3.17.77.84 1.24 1.91 1.24 3.23 0 4.64-2.8 5.67-5.48 5.97.43.37.81 1.1.81 2.22v3.29c0 .32.21.69.82.58A12 12 0 0012 .5z"/>
        </svg>
        @break

    @case('google')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 533.5 544.3', 'fill' => 'currentColor', 'aria-label' => $ariaLabel ?? 'Google']) }}>
            <path d="M533.5 278.4c0-18.6-1.6-37-4.7-54.7H272v103.5h147.2c-6.3 34.2-25.6 63.2-54.6 82.5v68h88.4c51.8-47.7 80.5-118 80.5-199.3z"/>
            <path d="M272 544.3c73.5 0 135.2-24.3 180.3-65.6l-88.4-68c-24.6 16.5-56.3 26-91.9 26-70.7 0-130.6-47.7-152-111.7h-92.7v70.2C71.9 483.7 165.7 544.3 272 544.3z"/>
            <path d="M120 325c-10-29.8-10-62.7 0-92.5v-70.2H27.3C-14.7 221.6-14.7 322.6 27.3 388.8L120 325z"/>
            <path d="M272 106.1c39.9-.6 78.5 14.8 107.9 43.3l80.6-80.6C406.8 22 343.6-.2 272 0 165.7 0 71.9 60.6 27.3 151.9L120 215c21.4-64 81.3-108.9 152-108.9z"/>
        </svg>
        @break

    @case('apple')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 24 24', 'fill' => 'currentColor', 'aria-label' => $ariaLabel ?? 'Apple']) }}>
            <path d="M16.36 1.64c0 1.14-.46 2.22-1.24 3a4.21 4.21 0 01-3.03 1.24c0-1.14.47-2.22 1.25-3a4.21 4.21 0 012.99-1.24h.03zM12.14 6.5c1.02 0 2.33-.7 3.5-.7 2.58 0 3.6 1.84 3.6 1.84s-1.99 1.02-1.99 3.5c0 2.8 2.46 3.76 2.46 3.76s-1.72 4.86-4.04 4.86c-1.06 0-1.89-.7-3.09-.7-1.23 0-2.1.7-3.12.7-2.06 0-5.05-4.73-5.05-8.93 0-3.91 2.54-5.59 4.91-5.59 1.53 0 2.8.76 3.82.76z"/>
        </svg>
        @break

    @case('microsoft')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 24 24', 'fill' => 'currentColor', 'aria-label' => $ariaLabel ?? 'Microsoft']) }}>
            <rect x="2" y="2" width="9" height="9" />
            <rect x="13" y="2" width="9" height="9" />
            <rect x="2" y="13" width="9" height="9" />
            <rect x="13" y="13" width="9" height="9" />
        </svg>
        @break

    @case('facebook')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 24 24', 'fill' => 'currentColor', 'aria-label' => $ariaLabel ?? 'Facebook']) }}>
            <path d="M22 12a10 10 0 10-11.5 9.88v-7h-2.4V12h2.4V9.8c0-2.37 1.41-3.69 3.56-3.69.73 0 1.5.13 1.5.13v2.32h-.85c-.84 0-1.1.52-1.1 1.06V12h2.5l-.4 2.88h-2.1v7A10 10 0 0022 12z"/>
        </svg>
        @break

    @case('twitter')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 24 24', 'fill' => 'currentColor', 'aria-label' => $ariaLabel ?? 'Twitter']) }}>
            <path d="M22.46 6c-.77.35-1.6.58-2.46.69a4.28 4.28 0 001.88-2.37 8.55 8.55 0 01-2.72 1.04 4.27 4.27 0 00-7.28 3.89A12.13 12.13 0 013 4.79a4.27 4.27 0 001.32 5.7 4.24 4.24 0 01-1.93-.53v.05a4.27 4.27 0 003.43 4.19 4.3 4.3 0 01-1.92.07 4.27 4.27 0 003.98 2.96A8.57 8.57 0 012 19.54 12.08 12.08 0 008.29 21c7.55 0 11.68-6.26 11.68-11.68 0-.18-.01-.35-.02-.53A8.35 8.35 0 0022.46 6z"/>
        </svg>
        @break

    @case('discord')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 245 240', 'fill' => 'currentColor', 'aria-label' => $ariaLabel ?? 'Discord']) }}>
            <path d="M104.4 104.9c-5.7 0-10.2 5-10.2 11.1 0 6.1 4.6 11.1 10.2 11.1 5.7 0 10.2-5 10.2-11.1.1-6.1-4.5-11.1-10.2-11.1zm36.2 0c-5.7 0-10.2 5-10.2 11.1 0 6.1 4.6 11.1 10.2 11.1 5.7 0 10.2-5 10.2-11.1 0-6.1-4.5-11.1-10.2-11.1z"/>
            <path d="M189.5 20h-134C38 20 24 34 24 51.4v137.2C24 206 38 220 55.5 220H168l-5.9-20.5 14.3 13.2 13.5 12.4 24.6 21.9V51.4C214.5 34 200.5 20 183 20h6.5zM162 162s-5.3-6.3-9.7-11.9c19.3-5.5 26.6-17.6 26.6-17.6-6 4-11.7 6.8-16.8 8.7-7.3 3.1-14.3 5.1-21.1 6.3-14 2.6-26.8 1.9-37.8-.1-8.3-1.6-15.4-3.9-21.3-6.3-3.3-1.3-6.9-3-10.5-5.3-.4-.3-.8-.5-1.2-.8-.3-.2-.5-.3-.7-.5-.3-.2-.5-.3-.5-.3s7.1 11.9 26 17.5c-4.4 5.6-9.8 12-9.8 12-32.3-1-44.6-22.2-44.6-22.2 0-47 21-85 21-85 21-16 41.2-15.6 41.2-15.6l1.5 1.8c-26.4 7.5-38.5 19-38.5 19s3.2-1.8 8.6-4.5c15.6-6.9 28-8.9 33.1-9.4.8-.1 1.5-.2 2.3-.2 8.2-1.1 17.3-1.4 26.8-.3 12.6 1.5 26.1 5.4 39.8 13.3 0 0-11.6-11-36.7-18.5l2-2.3s20.2-.4 41.2 15.6c0 0 21 38 21 85 0 0-12.3 21.2-44.6 22.2z"/>
        </svg>
        @break

    @case('gitlab')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 24 24', 'fill' => 'currentColor', 'aria-label' => $ariaLabel ?? 'GitLab']) }}>
            <path d="M22.65 13.39l-2.27-7a.47.47 0 00-.9 0l-1.52 4.67H6.04L4.52 6.39a.47.47 0 00-.9 0l-2.27 7a1.62 1.62 0 00.59 1.83l9.06 6.6a1.2 1.2 0 001.41 0l9.06-6.6a1.62 1.62 0 00.58-1.83z"/>
        </svg>
        @break

    @case('linkedin')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 24 24', 'fill' => 'currentColor', 'aria-label' => $ariaLabel ?? 'LinkedIn']) }}>
            <path d="M4.98 3.5C4.98 4.88 3.86 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM.5 8.5h4V24h-4zM8.5 8.5h3.8v2.1h.05c.53-1 1.84-2.1 3.8-2.1 4.07 0 4.82 2.68 4.82 6.16V24h-4v-6.9c0-1.64 0-3.76-2.3-3.76-2.3 0-2.65 1.8-2.65 3.64V24h-4z"/>
        </svg>
        @break

    @case('slack')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 24 24', 'fill' => 'currentColor', 'aria-label' => $ariaLabel ?? 'Slack']) }}>
            <path d="M14.5 0a2 2 0 012 2v2.5a2 2 0 11-4 0V2a2 2 0 012-2zM22 9.5a2 2 0 01-2 2h-2.5a2 2 0 110-4H20a2 2 0 012 2zM9.5 22a2 2 0 01-2-2v-2.5a2 2 0 114 0V20a2 2 0 01-2 2zM2 14.5a2 2 0 012-2h2.5a2 2 0 110 4H4a2 2 0 01-2-2z"/>
            <path d="M7 2a2 2 0 012 2v5a2 2 0 11-4 0V4a2 2 0 012-2zm15 7a2 2 0 01-2-2h-5a2 2 0 110-4h5a2 2 0 012 2zM9 24a2 2 0 01-2-2h5a2 2 0 110 4H7a2 2 0 01-2-2zm-7-9a2 2 0 012-2v5a2 2 0 11-4 0v-5z"/>
        </svg>
        @break

    @case('steam')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 24 24', 'fill' => 'currentColor', 'aria-label' => $ariaLabel ?? 'Steam']) }}>
            <path d="M12 0a12 12 0 1011.3 8.4A7 7 0 0112 21a5.5 5.5 0 01-4.06-1.75l2.46 1a3.5 3.5 0 104.48-4.38l2.5-1A7 7 0 1112 0z"/>
        </svg>
        @break

    @case('spotify')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 24 24', 'fill' => 'currentColor', 'aria-label' => $ariaLabel ?? 'Spotify']) }}>
            <path d="M12 0a12 12 0 1012 12A12 12 0 0012 0zm5.19 17.36a.75.75 0 01-1.03.26 10.3 10.3 0 00-10.32 0 .75.75 0 11-.77-1.29 11.8 11.8 0 0111.86 0 .75.75 0 01.26 1.03zM18 14.3a.9.9 0 01-1.23.3 12.9 12.9 0 00-13 0 .9.9 0 11-.92-1.55 14.7 14.7 0 0114.84 0A.9.9 0 0118 14.3zm.19-3.36a1.05 1.05 0 01-1.43.35 15.4 15.4 0 00-15.37 0 1.05 1.05 0 11-1.05-1.82 17.1 17.1 0 0117.47 0 1.05 1.05 0 01.38 1.47z"/>
        </svg>
        @break

    @case('yahoo')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 24 24', 'fill' => 'currentColor', 'aria-label' => $ariaLabel ?? 'Yahoo']) }}>
            <path d="M6 3l4 9H7l-1 3h5l2 6h4L12 3H6zm12 6l-1 3h3l-1 3h-3l-1 3h-2l3-9h2z"/>
        </svg>
        @break

    @case('wechat')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 50 50', 'fill' => 'currentColor', 'aria-label' => $ariaLabel ?? 'WeChat']) }}>
            <path d="M16 22a8 8 0 1016 0 8 8 0 10-16 0zm-6 0a14 14 0 1128 0 14 14 0 11-28 0z"/>
        </svg>
        @break

    @case('metamask')
        <svg {{ $attributes->merge(['class' => $base, 'viewBox' => '0 0 24 24', 'fill' => 'currentColor', 'aria-label' => $ariaLabel ?? 'MetaMask']) }}>
            <path d="M3 2l9 7 9-7-4 10 4 9-9-5-9 5 4-9L3 2z"/>
        </svg>
        @break

    @default
        {{ $slot }}
@endswitch


