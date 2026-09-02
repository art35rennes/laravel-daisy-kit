---
name: laravel-daisy-kit-development
description: Build, review, or test Laravel Daisy Kit's focused Blade modules and their explicit frontend entries.
---

# Laravel Daisy Kit Development

## When to use this skill

Use this skill when installing, composing, extending, reviewing, or testing Laravel Daisy Kit
in a PHP 8.4 and Laravel 13 application. Use the official `laravel-best-practices` skill as well
when it is available through Laravel Boost; it owns generic Laravel guidance.

## Public boundary

Only these Blade components are supported:

- `x-daisy-kit::table`
- `x-daisy-kit::tree`
- `x-daisy-kit::blueprint`
- `x-daisy-kit::file-preview`
- `x-daisy-kit::map`
- `x-daisy-kit::copyable`
- `x-daisy-kit::combobox`
- `x-daisy-kit::signature`
- `x-daisy-kit::truncate`
- `x-daisy-kit::scrollspy`
- `x-daisy-kit::transfer-list`

Do not introduce aliases, additional primitive DaisyUI wrappers, host templates, routes,
controllers, facades, migrations, asset publication, Forms or Livewire integration, or a
compatibility layer.

## Assets and lifecycle

The host application installs and compiles DaisyUI and Tailwind CSS. This package is installed by
Composer/VCS, not npm. Configure this Vite alias once in the host application's `vite.config.js`:

```js
import { resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = fileURLToPath(new URL('.', import.meta.url));

// Inside defineConfig({ resolve: { alias: { ... } } }):
'@daisy-kit': resolve(__dirname, 'vendor/art35rennes/laravel-daisy-kit/dist'),
```

Then import the ESM and CSS entry for every module used on a page, for example:

```js
import '@daisy-kit/table.css';
import { mountAll } from '@daisy-kit/table.js';

mountAll();
```

The entry stems are `table`, `tree`, `blueprint`, `file-preview`, `map`, `copyable`, `combobox`,
`signature`, `truncate`, `scrollspy`, and `transfer-list`; use `@daisy-kit/{stem}.js` and
`@daisy-kit/{stem}.css` only for modules rendered on the page.

Each entry independently exposes `mount(root)`, `mountAll(scope = document)`, `unmount(root)`,
and `getInstance(root)`. `mount` returns a stable facade or `null`; `mountAll` returns facade-or-null
results in DOM order; `getInstance` returns the exact mounted facade; and `unmount` reports whether
it removed an instance. Keep mounting idempotent, support multiple roots, and destroy listeners,
observers, requests, and third-party instances on unmount. Facade getters return detached snapshots.
Synchronous commands return booleans, asynchronous commands return `Promise<boolean>`, and expected
operational failures return `false` plus `daisy-kit:{module}:error` with `{ code, message, ...context }`.
Do not create globals or implicit imports between modules. Public events use only
`daisy-kit:{module}:*`.

Tree exposes `getValue`, `getState`, `setValue`, `clear`, `expand`, `collapse`, `focus`,
`setSearch`, `applySearch`, `clearSearch`, `setFilter`, `clearFilter`, `expandPath`, `expandAll`,
`collapseAll`, `selectVisible`, `loadMore`, and `reloadBranch`; see `docs/tree.md` for selection scope,
host filters, highlighting and paginated lazy contracts. Blueprint exposes
`getValue`, `setValue`, `getSelected`, `select`, `undo`, `redo`, `arrange`, and `fit`; File Preview
exposes `getState`, `open`, `close`, `setExpanded`, `setZoom`, and `reload`. Do not expose private
TanStack, Dagre, iframe, SignaturePad, or SortableJS instances. `Map.getLeafletMap()` remains the sole
documented third-party escape hatch.

Combobox opens available suggestions when its input or control is activated. Set `minChars=0` to
load a remote first page on open, and keep `maxSuggestions` bounded. Its safe option shape supports
`description`, `meta`, `initials`, and `avatar` for person/e-mail pickers. For richer host markup,
use `setOptionRenderer(renderer)`; return a DOM node or plain text and leave the outer option role,
selection state, and keyboard handling to Daisy Kit. Never serialize HTML in option data.

## Product outcomes

Treat `docs/specs/v5-product-contract-matrix.md` as the package's single business-contract
oracle. A module is not complete merely because it reaches `ready`: test the user outcome.
Table and Tree preserve data-selection workflows; Blueprint preserves an accessible editor and
synchronized JSON; File Preview preserves isolated media/document actions; Map preserves editable
layers and spatial tools. Copyable, Combobox, Signature, Truncate, Scrollspy and Transfer list
retain their independently mounted interaction outcomes. Keep generic DaisyUI primitives in the
host rather than reintroducing wrappers.

## Configuration and CSP

Pass complex component configuration as escaped, non-executable JSON; reject invalid JSON with
an accessible error state. Do not add inline scripts, handlers, executable configuration,
`eval`, inline styles, or template-authored `<style>` blocks.

Do not assume `crypto.randomUUID()` exists on an HTTP development origin. Use the shared
instance identifier helper for DOM identity and preserve a structured error event when module
initialization fails. File Preview still authenticates its opaque-origin child by both frame
source and a per-instance token; never add `allow-same-origin`.

The host policy remains strict for the core modules. File Preview handles untrusted documents in
a `srcdoc` sandboxed iframe without `allow-same-origin`; its two external child chunks are emitted
by Vite from the `@daisy-kit/file-preview.js` import, so do not add a route, proxy, copy step, or
asset publication. Keep document scripts, forms, navigation, and unnecessary network access
disabled. Validate file type and size, and release frames, listeners, requests, and renderer
resources on destruction.

SignaturePad and SortableJS write runtime DOM styles. A page mounting Signature or Transfer List
must allow the page-wide `style-src-attr 'unsafe-inline'` exception and should keep that page
surface narrow. All other parent-page entries retain `style-src-attr 'none'`. Do not add TanStack
Virtual; v5 deliberately avoids its inline-style cost and does not promise large remote transfer
datasets.

## Verification

For package development, run the narrow relevant test first, then the repository quality gates:

```bash
npm run test:js
composer test:types
composer test:full
composer test:tia:fresh
```

Pest 5 Test Impact Analysis speeds iteration but never replaces `composer test:full`, the
cache-independent release suite. Browser tests must cover actual CSP violations, lifecycle,
keyboard/focus behavior, multiple instances, and responsive states where they apply.

The Testbench Workbench is an internal Laravel host, not product documentation. It may render
representative Blade, native forms, explicit Vite entries, and deterministic local endpoints. Do
not add a facade console, event logger, inspector, or visible control that exists only for a test;
exercise facades in Vitest or through browser-test scripts instead.

Before changing the public boundary, record the decision in `docs/decisions/` and update
`docs/specs/v5-public-contract.md`. Keep `dist/` reproducible and tracked; do not track
dependency directories, TIA results, or Workbench build artifacts.
