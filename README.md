# Laravel Daisy Kit

Laravel Daisy Kit v6 is a small set of explicitly mounted Blade modules for Laravel 13
applications that already compile Tailwind CSS and DaisyUI. It is a clean break from the
legacy v4 line: it has no aliases, adapters, or migration layer.

## Requirements

- PHP 8.4+
- Laravel / Illuminate 13
- Tailwind CSS and DaisyUI configured by the host application

PHP 8.4 is required consistently for runtime, Pest 5 development, and CI.

Install it from GitHub/VCS rather than Packagist:

```json
{
    "repositories": [{ "type": "vcs", "url": "https://github.com/art35rennes/laravel-daisy-kit" }],
    "require": { "art35rennes/laravel-daisy-kit": "^6.0" }
}
```

## Public components

`x-daisy-kit::table`, `x-daisy-kit::tree`, `x-daisy-kit::blueprint`,
`x-daisy-kit::file-preview`, `x-daisy-kit::map`, `x-daisy-kit::copyable`,
`x-daisy-kit::combobox`, `x-daisy-kit::signature`, `x-daisy-kit::truncate`,
`x-daisy-kit::scrollspy`, and `x-daisy-kit::transfer-list` are the complete public surface.
Their contracts are documented in
[`docs/specs/v6-public-contract.md`](docs/specs/v6-public-contract.md).

## Explicit assets

Composer installs this package under `vendor/art35rennes/laravel-daisy-kit`; it is not an npm
package. Configure this stable Vite alias in the host application's `vite.config.js`:

```js
import { defineConfig } from 'vite';
import { resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = fileURLToPath(new URL('.', import.meta.url));

export default defineConfig({
    resolve: {
        alias: {
            '@daisy-kit': resolve(__dirname, 'vendor/art35rennes/laravel-daisy-kit/dist'),
        },
    },
});
```

Import only the JavaScript and CSS modules used on a host page through that alias. For example:

```js
import '@daisy-kit/table.css';
import { mountAll } from '@daisy-kit/table.js';

mountAll();
```

The available pairs are `table`, `tree`, `blueprint`, `file-preview`, `map`, `copyable`,
`combobox`, `signature`, `truncate`, `scrollspy`, and `transfer-list`. Do not import this
Composer package by its package name in a Vite source file.

[`docs/examples.md`](docs/examples.md) contains copyable Blade and Vite examples for every
module, including their common options and stateful use cases.

Each ESM entry exports `mount(root)`, `mountAll(scope = document)`, `unmount(root)`, and
`getInstance(root)`. `mount` returns a stable module facade, repeated mounts and `getInstance`
return the same object, and `mountAll` returns facades in DOM order. Getters return detached
snapshots; synchronous commands return booleans and asynchronous commands return
`Promise<boolean>`. Operational failures return `false` and emit a structured
`daisy-kit:{module}:error` event. Lifecycle teardown is available only through `unmount(root)`;
the internal `destroy` hook is not part of any facade. The complete facade and event payload contract is in the
[public contract](docs/specs/v6-public-contract.md).

There is no global bootstrap or `vendor:publish` step. Configuration is
rendered as encoded JSON, with no inline script or handler. Signature and Transfer List require
`style-src-attr 'unsafe-inline'` on pages that mount them because their pinned third-party
dependencies write DOM style properties; the other entries retain the stricter policy.
`@daisy-kit/file-preview.js` also causes Vite to emit File Preview's sandboxed-frame chunks;
do not add a route, proxy, copy step, or manual asset import for them.

Dependency license texts are included in [THIRD_PARTY_NOTICES.md](THIRD_PARTY_NOTICES.md).

## Development

Use Node.js 24 and npm with the committed lock file for reproducible builds.

```bash
composer install
npm ci
npx playwright install chromium
composer test
npm run test:js
npm run build
```

`composer build:workbench` prepares the Testbench Workbench. The tracked `dist/` directory
is the reproducible runtime distribution; dependencies, coverage, and Workbench build output
are not tracked.

The Workbench is deliberately a representative internal Laravel host: it renders normal Blade,
uses explicit Vite entries, local routes and native forms, and supports browser outcome tests. It
is not an API explorer or interactive documentation surface; facade examples belong in
[`docs/examples.md`](docs/examples.md).

`composer test:full` always runs the complete Pest suite, including the Workbench browser check,
with TIA disabled. `composer test:tia`
uses Pest 5 Test Impact Analysis for local iteration; its graph and cached results live under
the ignored `tests/.pest/` directory. TIA is deliberately serial because the suite includes a
real Testbench browser test with its own HTTP server; this prevents parallel workers from
racing that server while recording every test in the graph. Both TIA commands rebuild the
Testbench Workbench before testing, so CI never relies on generated local state. CI records a
fresh, complete baseline artifact. `composer test:full` remains the cache-independent release gate.

## AI agent resources

This package ships concise, distributable Laravel Boost guidance for consuming applications:
`resources/boost/guidelines/core.blade.php` is loaded as foundational context and
`resources/boost/skills/laravel-daisy-kit-development/` is an on-demand package skill.
With Laravel Boost 2.7+ installed in the consuming application, run:

```bash
php artisan boost:install --guidelines --skills --mcp
php artisan boost:update --discover
```

Boost's generated agent files are host-local state; this repository versions only the package
resources and its own [`AGENTS.md`](AGENTS.md) conventions. The package skill complements the
official `laravel-best-practices` skill when Boost makes it available.

## Stable release and upgrading

`v6.0.0` is the stable eleven-module contract, distributed through GitHub/VCS.
It has no compatibility layer for v5.0.0 or its historical alpha releases.
Forms Viewer/Builder and the package Livewire integration were removed. Applications
must own their forms and any Livewire integration; changing a Composer constraint alone
is not a migration. See [the upgrade guide](docs/upgrading-to-v6.md) and
[release notes](docs/releases/v6.0.0.md).

The [executable demo](https://github.com/art35rennes/laravel-daisy-kit-demo/tree/v6.0.0)
locks the same release and includes local installation instructions. No hosted demo is required.

Existing v4 applications can remain on `v4.0.0` / `legacy/4.x` until they adopt the new API.
Report reproducible bugs through [GitHub issues](https://github.com/art35rennes/laravel-daisy-kit/issues),
including the installed tag, PHP/browser versions, module configuration and a minimal example.
Do not include secrets or private documents.
