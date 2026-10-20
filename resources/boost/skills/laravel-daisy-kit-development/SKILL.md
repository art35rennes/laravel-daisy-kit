---
name: laravel-daisy-kit-development
description: Build, review, or test Laravel Daisy Kit's focused Blade modules and their explicit frontend entries.
---

# Laravel Daisy Kit Development

## When to use this skill

Use this skill when installing, composing, extending, reviewing, or testing Laravel Daisy Kit
in a PHP 8.4 and Laravel 13 application. Follow the host application's instructions, approved
runtime and permission rules. Use the official `laravel-best-practices` skill for generic Laravel
guidance only when it is already approved and available in that environment. Availability through
Laravel Boost does not authorize installing or activating another skill, plugin or MCP server.
Complete any review or approval required by the host before doing so; this skill grants none.

## Package documentation and working directories

Resolve the installed Composer package's location before reading package documentation. In a
host application the default package root is `vendor/art35rennes/laravel-daisy-kit`; use the actual
Composer installation location if the host configures a different vendor directory. In a dedicated
Laravel Daisy Kit source checkout, the package root is that checkout's root.

Every `docs/` path below is relative to the package root, not the host application's root or the
directory where Boost copied this skill. For example, the Tree reference in a normal host is
`vendor/art35rennes/laravel-daisy-kit/docs/tree.md`; in the package checkout it is `docs/tree.md`.
If a referenced package document is missing, report it rather than substituting a similarly named
host document. Host configuration paths, such as `vite.config.js`, belong to the host application.

Do not edit the installed package under vendor. Make generic module fixes in a dedicated package
source checkout and integrate an approved version through the host's normal dependency workflow.

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
- `x-daisy-kit::code-editor`
- `x-daisy-kit::wysiwyg`

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
`signature`, `truncate`, `scrollspy`, `transfer-list`, `code-editor`, and `wysiwyg`; use `@daisy-kit/{stem}.js` and
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
selection state, and keyboard handling to Daisy Kit. Use `searchFields` for local canonical-field
matching, `setOptions()` for a changing client-owned catalogue, and `source` for a Laravel endpoint
that owns its server-side search columns. Remote result pages preserve the metadata of selected
tokens. Never serialize HTML in option data.

## Product outcomes

Treat `docs/specs/v6-product-contract-matrix.md` as the package's single business-contract
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

SignaturePad, SortableJS and Trix write runtime DOM styles. A page mounting Signature, Transfer List
or WYSIWYG must allow the page-wide `style-src-attr 'unsafe-inline'` exception and should keep that
page surface narrow. WYSIWYG also needs the Trix nonce in `style-src` and `img-src blob:` for local
attachment previews. All other parent-page entries retain `style-src-attr 'none'`. Do not add TanStack
Virtual; v6 deliberately avoids its inline-style cost and does not promise large remote transfer
datasets.

## Verification

In a host application, use its existing tests, approved runtime and verification commands. Do not
run the package maintenance commands below from the host root or its installed vendor package.

Only in a dedicated package development checkout, with its dependencies and runtime already
approved, run the narrow relevant test first and the package quality gates required for the change:

```bash
npm run test:js
composer test:types
composer test:full
```

Pest 5 Test Impact Analysis speeds iteration but never replaces `composer test:full`, the
cache-independent release suite. `composer test:tia:fresh` is the supplementary fresh-baseline
command for package development. These commands can rebuild tracked dist output, temporary and
Workbench assets, Workbench storage/database files and test caches. Preserve unrelated changes
and follow the checkout's rules before running them; do not bootstrap or install dependencies
merely to execute these examples. Browser tests must cover actual CSP violations, lifecycle,
keyboard/focus behavior, multiple instances, and responsive states where they apply.

The Testbench Workbench is an internal Laravel host, not product documentation. It may render
representative Blade, native forms, explicit Vite entries, and deterministic local endpoints. Do
not add a facade console, event logger, inspector, or visible control that exists only for a test;
exercise facades in Vitest or through browser-test scripts instead.

For package development, before changing the public boundary record the decision in the package's
`docs/decisions/` and update its `docs/specs/v6-public-contract.md`. This does not request new
documentation in the host application. Keep package `dist/` reproducible and tracked; do not track
dependency directories, TIA results, or Workbench build artifacts. Follow the repository's release
and approval rules; these verification examples do not authorize publication or deployment.

## Code Editor

Use `x-daisy-kit::code-editor` with explicit `@daisy-kit/code-editor.js` and
`@daisy-kit/code-editor.css` imports. Supply a response nonce for generated styles.
Keep the CodeMirror engine private. It edits one document, synchronizes a native
textarea, loads language grammars on demand and inherits the nearest DaisyUI theme.
Read-only allows selection, search and copy. setValue replaces code and resets
history. It does not execute code or save it remotely.

## WYSIWYG

Use `x-daisy-kit::wysiwyg` with explicit `@daisy-kit/wysiwyg.js` and
`@daisy-kit/wysiwyg.css` imports. Trix is the supported engine and
`getTrixEditor()` is the advanced escape hatch. Browser sanitization does not
replace host-side sanitization of submitted HTML. Attachments are disabled by
default; when enabled, the host uploads each emitted File and resolves it through
the facade. Do not add an upload route, storage adapter, asset publication or
Livewire integration to the package.
