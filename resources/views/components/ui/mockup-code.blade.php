@props([
    // Tableau de lignes à rendre automatiquement
    // Chaque entrée peut être une string (texte simple) ou un array avec clés:
    //  - text: contenu de la ligne
    //  - prefix: valeur pour l'attribut data-prefix (ex: "$", ">", "1")
    //  - class: classes tailwind (ex: "text-warning")
    //  - highlight: couleur de surbrillance (ex: "warning" => bg-warning text-warning-content)
    'lines' => null,
])

@php
    $containerClasses = $attributes->get('class');
    // retire la classe du merge automatique pour l'ajouter proprement plus bas
    $attributes = $attributes->except('class');
@endphp

<div {{ $attributes->merge(['class' => trim('mockup-code '.($containerClasses ?? ''))]) }}>
  @if(is_array($lines))
    @foreach($lines as $line)
      @php
        if (is_string($line)) {
            $line = ['text' => $line];
        }
        $text = $line['text'] ?? '';
        $prefix = $line['prefix'] ?? null;
        $lineClass = $line['class'] ?? '';
        if (!empty($line['highlight'])) {
            $color = $line['highlight'];
            $lineClass = trim(($lineClass ? $lineClass.' ' : '').'bg-'.$color.' text-'.$color.'-content');
        }
      @endphp
      <pre @if($prefix !== null) data-prefix="{{ $prefix }}" @endif class="{{ $lineClass }}"><code>{{ $text }}</code></pre>
    @endforeach
  @else
    {{ $slot }}
  @endif
</div>
