<div {{ $attributes->merge(['class' => 'navbar bg-base-100']) }}>
    <div class="navbar-start">
        {{ $start ?? '' }}
    </div>
    <div class="navbar-center">
        {{ $center ?? '' }}
    </div>
    <div class="navbar-end">
        {{ $end ?? '' }}
    </div>
</div>
