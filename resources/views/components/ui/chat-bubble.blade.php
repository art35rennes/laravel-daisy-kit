@props([
    'align' => 'start', // start|end
    'name' => null,
    'time' => null,
    'color' => null, // neutral|primary|secondary|accent|info|success|warning|error
])

@php
    $bubbleClasses = 'chat-bubble';
    $validColors = ['neutral','primary','secondary','accent','info','success','warning','error'];
    if ($color && in_array($color, $validColors, true)) {
        $bubbleClasses .= ' chat-bubble-' . $color;
    }
@endphp

<div class="chat chat-{{ $align }}">
  @isset($avatar)
    <div class="chat-image avatar">
      <div class="w-10 rounded-full">{{ $avatar }}</div>
    </div>
  @endisset
  @if(isset($header) || $name || $time)
    <div class="chat-header">
      @isset($header)
        {{ $header }}
      @else
        {{ $name }}
        @if($time)
          <time class="text-xs opacity-50">{{ $time }}</time>
        @endif
      @endisset
    </div>
  @endif
  <div class="{{ $bubbleClasses }}">{{ $slot }}</div>
  @isset($footer)
    <div class="chat-footer opacity-50">{{ $footer }}</div>
  @endisset
</div>
