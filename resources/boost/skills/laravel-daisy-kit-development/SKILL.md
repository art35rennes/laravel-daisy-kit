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

The host application installs and compiles DaisyUI and Tailwind CSS. Import the package's ESM and
CSS entry for every module used on a page, for example:

```js
import 'art35rennes/laravel-daisy-kit/dist/table.css';
import { mountAll } from 'art35rennes/laravel-daisy-kit/dist/table.js';

mountAll();
```

Each entry independently exposes `mount(root)`, `mountAll(scope = document)`, and
`unmount(root)`. Keep mounting idempotent, support multiple roots, and destroy listeners,
observers, and third-party instances on unmount. Do not create globals or implicit imports
between modules. Public events use only `daisy-kit:{module}:*`.

## Configuration and CSP

Pass complex component configuration as escaped, non-executable JSON; reject invalid JSON with
an accessible error state. Do not add inline scripts, handlers, executable configuration,
`eval`, inline styles, or template-authored `<style>` blocks.

The host policy remains strict for the core modules. File Preview handles untrusted documents in
a separately loaded sandboxed iframe without `allow-same-origin`; keep document scripts, forms,
navigation, and unnecessary network access disabled. Validate file type and size, and release
frames, listeners, requests, and renderer resources on destruction.

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
