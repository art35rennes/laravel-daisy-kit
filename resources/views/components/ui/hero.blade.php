@props([
    'overlay' => false,
    'imageUrl' => null,
    'minH' => 'min-h-[24rem]',
])

<div {{ $attributes->merge(['class' => 'hero '.$minH]) }} @if($imageUrl) style="background-image: url('{{ $imageUrl }}');" @endif>
  @if($overlay)
    <div class="hero-overlay bg-opacity-60"></div>
  @endif
  <div class="hero-content text-center text-neutral-content">
    <div class="max-w-md">
      {{ $slot }}
    </div>
  </div>
</div>
