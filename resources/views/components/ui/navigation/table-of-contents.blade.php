@props([
    'sections' => [],  // [{ id, label }]
    'scrollSpy' => true,
])

<div class="hidden lg:block" @if($scrollSpy) data-module="table-of-contents" @endif>
    <div class="text-sm font-semibold mb-2 opacity-70">Sur cette page</div>
    <ul class="menu menu-xs">
        @foreach($sections as $s)
            @php
                $id = (string)($s['id'] ?? '');
                $label = (string)($s['label'] ?? $id);
            @endphp
            @if($id !== '')
                <li><a href="#{{ $id }}">{{ $label }}</a></li>
            @endif
        @endforeach
    </ul>
</div>
