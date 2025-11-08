@props([
    // Tableau de lignes à rendre automatiquement
    // Chaque entrée peut être une string (texte simple) ou un array avec clés:
    //  - text: contenu de la ligne
    //  - prefix: valeur pour l'attribut data-prefix (ex: "$", ">", "1")
    //  - class: classes tailwind (ex: "text-warning")
    //  - highlight: couleur de surbrillance (ex: "warning" => bg-warning text-warning-content)
    'lines' => null,
    // Afficher un bouton copier
    'copy' => false,
    // ID unique pour le bouton copier (généré automatiquement si non fourni)
    'copyId' => null,
])

@php
    $containerClasses = $attributes->get('class');
    // retire la classe du merge automatique pour l'ajouter proprement plus bas
    $attributes = $attributes->except('class');
    $copyId = $copyId ?? ($copy ? 'copy-'.uniqid() : null);
    
    // Extraire le texte à copier
    $codeToCopy = '';
    if (is_array($lines)) {
        foreach ($lines as $line) {
            if (is_string($line)) {
                $codeToCopy .= $line."\n";
            } else {
                $codeToCopy .= ($line['text'] ?? '')."\n";
            }
        }
        $codeToCopy = trim($codeToCopy);
    } else {
        // Pour le slot, on devra utiliser JS pour extraire le texte
        $codeToCopy = null;
    }
@endphp

<div {{ $attributes->merge(['class' => trim('mockup-code '.($containerClasses ?? ''))]) }}>
  @if($copy)
    <div class="flex items-center justify-end gap-2 mb-2">
      <button 
        type="button" 
        class="btn btn-xs btn-ghost" 
        data-copy-button 
        data-copy-target="{{ $copyId }}"
        @if($codeToCopy !== null) data-copy-text="{{ htmlspecialchars($codeToCopy, ENT_QUOTES, 'UTF-8') }}" @endif
        title="Copier le code"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
        </svg>
        <span class="copy-text">Copier</span>
      </button>
    </div>
  @endif
  <div id="{{ $copyId }}" class="{{ $copy ? '' : '' }}">
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
</div>

@if($copy)
@pushOnce('scripts')
<script>
(function() {
  document.addEventListener('click', async function(e) {
    const btn = e.target.closest('[data-copy-button]');
    if (!btn) return;
    
    const targetId = btn.dataset.copyTarget;
    const copyText = btn.dataset.copyText;
    
    let text = copyText;
    if (!text && targetId) {
      const target = document.getElementById(targetId);
      if (target) {
        text = target.textContent || target.innerText || '';
      }
    }
    
    if (!text) return;
    
    try {
      await navigator.clipboard.writeText(text.trim());
      const copyTextEl = btn.querySelector('.copy-text');
      if (copyTextEl) {
        const original = copyTextEl.textContent;
        copyTextEl.textContent = 'Copié!';
        setTimeout(() => {
          copyTextEl.textContent = original;
        }, 2000);
      }
    } catch (err) {
      console.error('Erreur lors de la copie:', err);
    }
  });
})();
</script>
@endPushOnce
@endif
