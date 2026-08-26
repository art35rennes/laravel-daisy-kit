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

- `x-daisy-kit::forms.viewer`
- `x-daisy-kit::forms.builder`
- `x-daisy-kit::table`
- `x-daisy-kit::tree`
- `x-daisy-kit::blueprint`
- `x-daisy-kit::file-preview`
- `x-daisy-kit::map`

Do not introduce aliases, primitive DaisyUI wrappers, host templates, routes, controllers,
facades, migrations, asset publication, or a compatibility layer. Livewire enhancement belongs
only to Forms Builder and is optional when Livewire 4 is installed.

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

The entry stems are `forms-viewer`, `forms-builder`, `table`, `tree`, `blueprint`,
`file-preview`, and `map`; use `@daisy-kit/{stem}.js` and `@daisy-kit/{stem}.css` only for
modules rendered on the page.

Each entry independently exposes `mount(root)`, `mountAll(scope = document)`, and
`unmount(root)`. Keep mounting idempotent, support multiple roots, and destroy listeners,
observers, and third-party instances on unmount. Do not create globals or implicit imports
between modules. Public events use only `daisy-kit:{module}:*`.

## Product outcomes

Treat `docs/specs/v5-product-contract-matrix.md` as the package's single business-contract
oracle. A module is not complete merely because it reaches `ready`: test the user outcome.
Forms share one recursive schema between Viewer and the optional Livewire 4 Builder; Table and
Tree preserve data-selection workflows; Blueprint preserves an accessible editor and synchronized
JSON; File Preview preserves isolated media/document actions; Map preserves editable layers and
spatial tools. Keep generic DaisyUI primitives in the host rather than reintroducing wrappers.

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

Before changing the public boundary, record the decision in `docs/decisions/` and update
`docs/specs/v5-public-contract.md`. Keep `dist/` reproducible and tracked; do not track
dependency directories, TIA results, or Workbench build artifacts.
