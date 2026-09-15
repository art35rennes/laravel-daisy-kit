<svg aria-hidden="true" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    @switch($name)
        @case('bold') <path stroke-linecap="round" stroke-linejoin="round" d="M7 5h6a4 4 0 0 1 0 8H7V5Zm0 8h7a3 3 0 0 1 0 6H7v-6Z" /> @break
        @case('italic') <path stroke-linecap="round" d="M10 5h7M7 19h7M14 5 10 19" /> @break
        @case('strike') <path stroke-linecap="round" d="M6 12h12M9 8c0-2 1.5-3 4-3 2 0 3.5.8 4 2M8 16c.8 2 2.5 3 5 3 2.3 0 4-1.1 4-3" /> @break
        @case('link') <path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 0 0 7.1 0l1.4-1.4a5 5 0 0 0-7.1-7.1L10.6 5M14 11a5 5 0 0 0-7.1 0l-1.4 1.4a5 5 0 0 0 7.1 7.1l.8-.8" /> @break
        @case('heading') <path stroke-linecap="round" d="M5 5v14M15 5v14M5 12h10M19 9v10M17 11l2-2 2 2" /> @break
        @case('quote') <path stroke-linecap="round" stroke-linejoin="round" d="M6 8h4v4H7v4H4v-5a3 3 0 0 1 2-3Zm10 0h4v4h-3v4h-3v-5a3 3 0 0 1 2-3Z" /> @break
        @case('code') <path stroke-linecap="round" stroke-linejoin="round" d="m8 9-3 3 3 3m8-6 3 3-3 3m-2-9-4 12" /> @break
        @case('bullets') <path stroke-linecap="round" d="M9 7h10M9 12h10M9 17h10M5 7h.01M5 12h.01M5 17h.01" /> @break
        @case('numbers') <path stroke-linecap="round" d="M10 7h9M10 12h9M10 17h9M4 6h2v3M4 13h2l-2 3h2" /> @break
        @case('outdent') <path stroke-linecap="round" stroke-linejoin="round" d="M10 7h9M10 12h9M10 17h9m-6-2-3-3 3-3" /> @break
        @case('indent') <path stroke-linecap="round" stroke-linejoin="round" d="M10 7h9M10 12h9M10 17h9m-9-2 3-3-3-3" /> @break
        @case('attach') <path stroke-linecap="round" stroke-linejoin="round" d="m8 12 6.5-6.5a3 3 0 0 1 4.2 4.2l-8.5 8.5a4 4 0 0 1-5.7-5.7L13 4" /> @break
        @case('undo') <path stroke-linecap="round" stroke-linejoin="round" d="m9 8-4 4 4 4m-4-4h8a6 6 0 0 1 6 6" /> @break
        @case('redo') <path stroke-linecap="round" stroke-linejoin="round" d="m15 8 4 4-4 4m4-4h-8a6 6 0 0 0-6 6" /> @break
    @endswitch
</svg>
