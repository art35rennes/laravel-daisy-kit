# Laravel Daisy Kit

Laravel Daisy Kit fournit des composants Blade basés sur daisyUI v5 et Tailwind CSS v4. Ce package expose daisyUI via des composants Blade réutilisables avec un namespace `daisy::`.

## Namespace et structure

Tous les composants utilisent le namespace `daisy::` avec le chemin complet :

- `daisy::ui.inputs.*` : button, input, textarea, select, checkbox, radio, range, toggle, file-input, color-picker
- `daisy::ui.navigation.*` : breadcrumbs, menu, pagination, navbar, sidebar, tabs, steps, stepper
- `daisy::ui.layout.*` : card, hero, footer, divider, list, list-row, stack, grid-layout
- `daisy::ui.data-display.*` : badge, avatar, kbd, table, stat, progress, radial-progress, status, timeline
- `daisy::ui.overlay.*` : modal, drawer, dropdown, popover, popconfirm, tooltip
- `daisy::ui.media.*` : carousel, lightbox, media-gallery, embed, leaflet
- `daisy::ui.feedback.*` : alert, toast, loading, skeleton, callout
- `daisy::ui.utilities.*` : mockup-browser, mockup-code, mockup-phone, mockup-window, indicator, dock
- `daisy::ui.advanced.*` : calendar-*, chart, code-editor, filter, onboarding, scroll-status, scrollspy, transfer, tree-view, validator, login-button, wysiwyg, accordion, collapse, countdown, diff, fieldset, join, label, link, mask, rating, swap, theme-controller
- `daisy::layout.*` : app, docs

@verbatim
<code-snippet name="Syntaxe des composants" lang="blade">
<x-daisy::ui.inputs.button>Texte</x-daisy::ui.inputs.button>
<x-daisy::ui.layout.card title="Titre">Contenu</x-daisy::ui.layout.card>
</code-snippet>
@endverbatim

## Props Blade et conventions daisyUI

Les composants exposent les classes daisyUI via des props Blade. Les couleurs, tailles et variantes suivent les conventions daisyUI v5.

### Couleurs daisyUI

Tous les composants qui supportent `color` acceptent : `primary`, `secondary`, `accent`, `info`, `success`, `warning`, `error`, `neutral`. Ces couleurs s'adaptent automatiquement au thème daisyUI.

### Tailles

Les composants supportent `size` avec : `xs`, `sm`, `md` (défaut), `lg`, `xl`.

### Variantes

Les variantes dépendent du composant. Pour les boutons : `solid` (défaut), `outline`, `ghost`, `link`, `soft`, `dash`. Pour les inputs : `null` (normal), `ghost`.

@verbatim
<code-snippet name="Exemples de props" lang="blade">
<x-daisy::ui.inputs.button color="primary" size="lg" variant="outline">
    Bouton
</x-daisy::ui.inputs.button>

<x-daisy::ui.inputs.input type="email" size="md" color="success" />
</code-snippet>
@endverbatim

## Slots et icônes

Les composants supportent des slots nommés pour personnaliser le contenu. Les icônes utilisent Blade Icons avec Bootstrap Icons (préfixe `bi`).

@verbatim
<code-snippet name="Slots et icônes" lang="blade">
<x-daisy::ui.inputs.button>
    <x-slot:icon>
        <x-bi-heart class="w-5 h-5" />
    </x-slot:icon>
    J'aime
    <x-slot:iconRight>
        <x-bi-arrow-right class="w-5 h-5" />
    </x-slot:iconRight>
</x-daisy::ui.inputs.button>

<x-daisy::ui.layout.card title="Titre">
    <p>Contenu</p>
    <x-slot:actions>
        <x-daisy::ui.inputs.button>Action</x-daisy::ui.inputs.button>
    </x-slot:actions>
</x-daisy::ui.layout.card>
</code-snippet>
@endverbatim

## Composants courants

### Button

Props principales : `type`, `variant`, `color`, `size`, `wide`, `block`, `circle`, `square`, `loading`, `active`, `disabled`, `tag` (`button`|`a`), `href`, `target`. Slots : `icon`, `iconRight`.

### Input

Props principales : `type`, `size`, `variant`, `color`, `disabled`. Supporte `inputMask`, `mask`, `obfuscate` pour les masques de saisie.

### Modal

Props principales : `id`, `title`, `open`, `vertical` (`top`|`middle`|`bottom`), `horizontal` (`start`|`end`|`null`), `size` (`xs`|`sm`|`md`|`lg`|`xl`|`2xl`|...|`7xl`), `backdrop`, `scrollable`, `teleport`. Slots : `actions`. Utilise `<dialog>` HTML5. Ouvrir/fermer via `myModal.showModal()` et `myModal.close()`.

@verbatim
<code-snippet name="Modal" lang="blade">
<x-daisy::ui.overlay.modal id="my-modal" title="Confirmation">
    <p>Êtes-vous sûr ?</p>
    <x-slot:actions>
        <x-daisy::ui.inputs.button onclick="document.getElementById('my-modal').close()">
            Fermer
        </x-daisy::ui.inputs.button>
    </x-slot:actions>
</x-daisy::ui.overlay.modal>

<button onclick="document.getElementById('my-modal').showModal()">
    Ouvrir
</button>
</code-snippet>
@endverbatim

### Card

Props principales : `title`, `imageUrl`, `bordered`, `compact`, `side`, `imageFull`, `color`, `dash`, `size`, `imageAlt`, `imageClass`, `figureClass`. Slots : `figure`, `actions`.

### Alert

Props principales : `color`, `variant`, `icon`, `heading` (ou `title`), `text`, `inline`, `iconInHeading`, `vertical`, `horizontal`, `horizontalAt`. Slots : `actions`, `controls`.

## JavaScript et data-module

Les composants génèrent automatiquement `data-module` et les attributs dataset depuis les props Blade. Ne jamais ajouter manuellement `data-module` dans les templates.

Les props en camelCase (ex: `inputMask`, `obfuscateChar`) deviennent des `data-*` en kebab-case (ex: `data-input-mask`, `data-obfuscate-char`) si un module JS est requis.

Le JS core scanne `[data-module]` et route vers les modules correspondants avec les options extraites du dataset.

## Configuration

Le package peut être configuré via `config/daisy-kit.php` :

- `auto_assets` : bool, défaut `true`. Pousse automatiquement CSS/JS dans les stacks Blade.
- `use_vite` : bool, défaut `true`. Utilise Vite (manifest) ou un bundle statique.
- `icon_prefix` : string, défaut `'bi'`. Préfixe des icônes Blade Icons.
- `docs.enabled` : bool, défaut `true`. Active les routes de documentation.

## Publication

```bash
php artisan vendor:publish --tag=daisy-views    # Vues des composants
php artisan vendor:publish --tag=daisy-lang     # Traductions
php artisan vendor:publish --tag=daisy-config   # Configuration
php artisan vendor:publish --tag=daisy-src       # Sources JS/CSS
php artisan vendor:publish --tag=daisy-dev-views # Pages de démo/docs
```

## Règles strictes

1. **Compatibilité** : Utiliser uniquement daisyUI v5 et Tailwind CSS v4. Ne jamais utiliser de classes daisyUI v4 ou Tailwind v3.

2. **Pas de CSS custom** : Ne jamais embarquer de CSS personnalisé. Utiliser uniquement les classes Tailwind v4 + daisyUI v5.

3. **Props Blade uniquement** : Toujours utiliser les props Blade plutôt que les data-attributes. Les composants génèrent automatiquement les attributs nécessaires.

4. **Namespace complet** : Utiliser le namespace complet `daisy::ui.inputs.button`, pas `daisy::button`.

5. **Icônes** : Utiliser exclusivement Blade Icons avec Bootstrap Icons. Ne jamais ajouter de SVG personnalisés.

6. **Réutilisation** : Vérifier la présence d'un composant similaire avant d'en créer un nouveau.

7. **Référence daisyUI** : Pour les classes CSS et conventions daisyUI, se référer à la documentation officielle daisyUI v5. Ce package expose daisyUI via Blade, pas besoin de redocumenter toutes les classes.

## Documentation daisyUI

Ce package expose daisyUI v5. Pour connaître toutes les classes CSS disponibles, les variantes et les comportements, consulter la [documentation officielle daisyUI v5](https://daisyui.com). Les composants Blade suivent exactement les conventions daisyUI.
