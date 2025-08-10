@props([
    'align' => 'start', // start|end
    'name' => null,
    'time' => null,
])

<div class="chat chat-{{ $align }}">
  @isset($avatar)
    <div class="chat-image avatar">
      <div class="w-10 rounded-full">{{ $avatar }}</div>
    </div>
  @endisset
  <div class="chat-header">
    {{ $name }}
    @if($time)
      <time class="text-xs opacity-50">{{ $time }}</time>
    @endif
  </div>
  <div class="chat-bubble">{{ $slot }}</div>
  @isset($footer)
    <div class="chat-footer opacity-50">{{ $footer }}</div>
  @endisset
</div>
