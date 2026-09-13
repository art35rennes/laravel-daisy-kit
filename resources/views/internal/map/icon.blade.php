<svg aria-hidden="true" fill="none" height="20" viewBox="0 0 24 24" width="20">
    @switch($icon)
        @case('layers')
            <path d="m4 9 8-4 8 4-8 4-8-4Zm0 4 8 4 8-4M4 17l8 4 8-4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            @break
        @case('drawing')
            <path d="m4 20 4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10L4 20Zm10-12 3 3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            @break
        @case('selection')
            <path d="M5 3v14l4-4 3 7 3-1-3-7h6L5 3Z" stroke="currentColor" stroke-linejoin="round" stroke-width="2"/>
            @break
        @case('history')
            <path d="M4 12a8 8 0 1 0 2-5.3L4 9m0-5v5h5m3-2v5l3 2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            @break
        @case('fit')
            <path d="M4 9V4h5m6 0h5v5m0 6v5h-5M9 20H4v-5" stroke="currentColor" stroke-linecap="round" stroke-width="2"/>
            @break
        @case('location')
            <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Zm0-8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-linejoin="round" stroke-width="2"/>
            @break
        @case('fullscreen')
            <path d="M4 9V4h5m6 0h5v5m0 6v5h-5M9 20H4v-5" stroke="currentColor" stroke-linecap="round" stroke-width="2"/>
            @break
        @case('view')
            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Zm10 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            @break
        @default
            <path d="M5 7h14M5 12h14M5 17h14" stroke="currentColor" stroke-linecap="round" stroke-width="2"/>
    @endswitch
</svg>
