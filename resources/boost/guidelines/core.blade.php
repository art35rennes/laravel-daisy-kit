# Laravel Daisy Kit

Laravel Daisy Kit provides Blade components based on daisyUI v5 and Tailwind CSS v4. This package exposes daisyUI through reusable Blade components with a `daisy::` namespace.

## Namespace and structure

All components use the `daisy::` namespace with the full path:

- `daisy::ui.inputs.*` : button, input, textarea, select, checkbox, radio, range, toggle, file-input, color-picker
- `daisy::ui.navigation.*` : breadcrumbs, menu, pagination, navbar, sidebar, tabs, steps, stepper
- `daisy::ui.layout.*` : card, hero, footer, divider, list, list-row, stack, grid-layout
- `daisy::ui.data-display.*` : badge, avatar, kbd, table, stat, progress, radial-progress, status, timeline
- `daisy::ui.overlay.*` : modal, drawer, dropdown, popover, popconfirm, tooltip
- `daisy::ui.media.*` : carousel, lightbox, media-gallery, embed, leaflet
- `daisy::ui.feedback.*` : alert, toast, loading, skeleton, callout
- `daisy::ui.utilities.*` : mockup-browser, mockup-code, mockup-phone, mockup-window, indicator, dock
- `daisy::ui.communication.*` : chat-bubble, chat-header, chat-input, chat-messages, chat-sidebar, chat-widget, conversation-view, notification-item, notification-list, notification-bell, notification-filters
- `daisy::ui.advanced.*` : calendar-*, chart, code-editor, filter, onboarding, scroll-status, scrollspy, transfer, tree-view, validator, login-button, wysiwyg, accordion, collapse, countdown, diff, fieldset, join, label, link, mask, rating, swap, theme-controller
- `daisy::layout.*` : app, docs

@verbatim
<code-snippet name="Component syntax" lang="blade">
<x-daisy::ui.inputs.button>Text</x-daisy::ui.inputs.button>
<x-daisy::ui.layout.card title="Title">Content</x-daisy::ui.layout.card>
</code-snippet>
@endverbatim

## Blade props and daisyUI conventions

Components expose daisyUI classes via Blade props. Colors, sizes, and variants follow daisyUI v5 conventions.

### daisyUI colors

All components that support `color` accept: `primary`, `secondary`, `accent`, `info`, `success`, `warning`, `error`, `neutral`. These colors automatically adapt to the daisyUI theme.

### Sizes

Components support `size` with: `xs`, `sm`, `md` (default), `lg`, `xl`.

### Variants

Variants depend on the component. For buttons: `solid` (default), `outline`, `ghost`, `link`, `soft`, `dash`. For inputs: `null` (normal), `ghost`.

@verbatim
<code-snippet name="Props examples" lang="blade">
<x-daisy::ui.inputs.button color="primary" size="lg" variant="outline">
    Button
</x-daisy::ui.inputs.button>

<x-daisy::ui.inputs.input type="email" size="md" color="success" />
</code-snippet>
@endverbatim

## Slots and icons

Components support named slots to customize content. Icons use Blade Icons with Bootstrap Icons (prefix `bi`).

@verbatim
<code-snippet name="Slots and icons" lang="blade">
<x-daisy::ui.inputs.button>
    <x-slot:icon>
        <x-bi-heart class="w-5 h-5" />
    </x-slot:icon>
    Like
    <x-slot:iconRight>
        <x-bi-arrow-right class="w-5 h-5" />
    </x-slot:iconRight>
</x-daisy::ui.inputs.button>

<x-daisy::ui.layout.card title="Title">
    <p>Content</p>
    <x-slot:actions>
        <x-daisy::ui.inputs.button>Action</x-daisy::ui.inputs.button>
    </x-slot:actions>
</x-daisy::ui.layout.card>
</code-snippet>
@endverbatim

## Common components

### Button

Main props: `type`, `variant`, `color`, `size`, `wide`, `block`, `circle`, `square`, `loading`, `active`, `disabled`, `tag` (`button`|`a`), `href`, `target`. Slots: `icon`, `iconRight`.

### Input

Main props: `type`, `size`, `variant`, `color`, `disabled`. Supports `inputMask`, `mask`, `obfuscate` for input masks.

### Modal

Main props: `id`, `title`, `open`, `vertical` (`top`|`middle`|`bottom`), `horizontal` (`start`|`end`|`null`), `size` (`xs`|`sm`|`md`|`lg`|`xl`|`2xl`|...|`7xl`), `backdrop`, `scrollable`, `teleport`. Slots: `actions`. Uses HTML5 `<dialog>`. Open/close via `myModal.showModal()` and `myModal.close()`.

@verbatim
<code-snippet name="Modal" lang="blade">
<x-daisy::ui.overlay.modal id="my-modal" title="Confirmation">
    <p>Are you sure?</p>
    <x-slot:actions>
        <x-daisy::ui.inputs.button onclick="document.getElementById('my-modal').close()">
            Close
        </x-daisy::ui.inputs.button>
    </x-slot:actions>
</x-daisy::ui.overlay.modal>

<button onclick="document.getElementById('my-modal').showModal()">
    Open
</button>
</code-snippet>
@endverbatim

### Card

Main props: `title`, `imageUrl`, `bordered`, `compact`, `side`, `imageFull`, `color`, `dash`, `size`, `imageAlt`, `imageClass`, `figureClass`. Slots: `figure`, `actions`.

### Alert

Main props: `color`, `variant`, `icon`, `heading` (or `title`), `text`, `inline`, `iconInHeading`, `vertical`, `horizontal`, `horizontalAt`. Slots: `actions`, `controls`.

## JavaScript and data-module

Components automatically generate `data-module` and dataset attributes from Blade props. Never manually add `data-module` in templates.

Props in camelCase (e.g., `inputMask`, `obfuscateChar`) become `data-*` in kebab-case (e.g., `data-input-mask`, `data-obfuscate-char`) if a JS module is required.

The core JS scans `[data-module]` and routes to corresponding modules with options extracted from the dataset.

## Configuration

The package can be configured via `config/daisy-kit.php`:

- `auto_assets`: bool, default `true`. Automatically pushes CSS/JS into Blade stacks.
- `use_vite`: bool, default `true`. Uses Vite (manifest) or a static bundle.
- `icon_prefix`: string, default `'bi'`. Blade Icons prefix.
- `docs.enabled`: bool, default `true`. Enables documentation routes.

## Publishing

```bash
php artisan vendor:publish --tag=daisy-views    # Component views
php artisan vendor:publish --tag=daisy-lang     # Translations
php artisan vendor:publish --tag=daisy-config   # Configuration
php artisan vendor:publish --tag=daisy-src       # JS/CSS sources
php artisan vendor:publish --tag=daisy-dev-views # Demo/docs pages
```

## Strict rules

1. **Compatibility**: Use only daisyUI v5 and Tailwind CSS v4. Never use daisyUI v4 or Tailwind v3 classes.

2. **No custom CSS**: Never embed custom CSS. Use only Tailwind v4 + daisyUI v5 classes.

3. **Blade props only**: Always use Blade props rather than data-attributes. Components automatically generate necessary attributes.

4. **Full namespace**: Use the full namespace `daisy::ui.inputs.button`, not `daisy::button`.

5. **Icons**: Use exclusively Blade Icons with Bootstrap Icons. Never add custom SVGs.

6. **Reuse**: Check for a similar component before creating a new one.

7. **Atomic Design**: Follow Atomic Design principles (see `.cursor/rules/atomic-design.mdc`) - strict hierarchy, no duplication, placement by functional category.

8. **daisyUI reference**: For CSS classes and daisyUI conventions, refer to the official daisyUI v5 documentation. This package exposes daisyUI via Blade, no need to redocument all classes.

## daisyUI documentation

This package exposes daisyUI v5. To know all available CSS classes, variants, and behaviors, consult the [official daisyUI v5 documentation](https://daisyui.com). Blade components follow daisyUI conventions exactly.
