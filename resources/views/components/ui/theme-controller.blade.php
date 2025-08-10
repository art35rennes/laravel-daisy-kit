@props([
    'themes' => ['light','dark','cupcake','bumblebee','emerald','corporate','synthwave','retro','cyberpunk','valentine','halloween','garden','forest','aqua','lofi','pastel','fantasy','wireframe','black','luxury','dracula','cmyk','autumn','business','acid','lemonade','night','coffee','winter'],
    'value' => null,
    'name' => 'theme',
])

<div {{ $attributes->merge(['class' => 'join']) }}>
    @foreach($themes as $t)
        <input type="radio" name="{{ $name }}" value="{{ $t }}" class="btn join-item theme-controller" aria-label="{{ ucfirst($t) }}" @checked($value === $t) />
    @endforeach
  </div>
