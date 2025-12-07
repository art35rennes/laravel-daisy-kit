@props([
    'id',
    'title',
    'class' => 'mt-10',
])

<section id="{{ $id }}" class="{{ $class }}">
    @if($title)
        <h2>{{ $title }}</h2>
    @endif
    {{ $slot }}
</section>

