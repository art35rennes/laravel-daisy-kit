@props([
    'id' => 'drawer-toggle',
    'open' => false,
    'sideClass' => 'w-80',
])

<div {{ $attributes->merge(['class' => 'drawer']) }}>
  <input id="{{ $id }}" type="checkbox" class="drawer-toggle" @checked($open) />
  <div class="drawer-content">
    {{ $content ?? $slot }}
  </div>
  <div class="drawer-side">
    <label for="{{ $id }}" aria-label="close sidebar" class="drawer-overlay"></label>
    <ul class="menu p-4 bg-base-200 text-base-content {{ $sideClass }}">
      {{ $side ?? '' }}
    </ul>
  </div>
</div>
