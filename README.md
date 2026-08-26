# Laravel Daisy Kit

Laravel Daisy Kit v5 is a small set of explicitly mounted Blade modules for Laravel 13
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
    "require": { "art35rennes/laravel-daisy-kit": "v5.1.0-alpha.1" }
}
```

## Public components

`x-daisy-kit::forms.viewer`, `x-daisy-kit::forms.builder`, `x-daisy-kit::table`,
`x-daisy-kit::tree`, `x-daisy-kit::blueprint`, `x-daisy-kit::file-preview`, and
`x-daisy-kit::map` are the complete public surface. Their contracts are documented in
[`docs/specs/v5-public-contract.md`](docs/specs/v5-public-contract.md).

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

The available pairs are `forms-viewer`, `forms-builder`, `table`, `tree`, `blueprint`,
`file-preview`, and `map` — for example `@daisy-kit/forms-viewer.js` and
`@daisy-kit/forms-viewer.css`. Do not import this Composer package by its package name in a Vite
source file.

[`docs/examples.md`](docs/examples.md) contains copyable Blade and Vite examples for every
module, including their common options and stateful use cases.

Each ESM entry exports `mount(root)`, `mountAll(scope = document)`, and `unmount(root)`.
There is no global bootstrap or `vendor:publish` step. Configuration is rendered as encoded
JSON, so a host can keep a strict CSP without inline script, handler, or style exceptions.
`@daisy-kit/file-preview.js` also causes Vite to emit File Preview's sandboxed-frame chunks;
do not add a route, proxy, copy step, or manual asset import for them.

## Development

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

## Status

`v5.1.0-alpha.1` is a VCS-only corrective development prerelease; **validation propriétaire en
attente**. It is not a stable release and must be pinned by its exact tag for demo integration.
The corrective v5 contract deliberately has no compatibility layer for v5.0.0 or its historical
alpha releases: use this documentation and tag as one coherent development line, rather than
combining examples or runtime assumptions from earlier v5 tags.

Existing v4 applications should remain on
[`v4.0.0`](https://github.com/art35rennes/laravel-daisy-kit/releases/tag/v4.0.0) or the
`legacy/4.x` branch until they choose to adopt the new API.
