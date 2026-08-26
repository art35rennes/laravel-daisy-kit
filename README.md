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
    "require": { "art35rennes/laravel-daisy-kit": "v5.0.0-alpha.1" }
}
```

## Public components

`x-daisy-kit::forms.viewer`, `x-daisy-kit::forms.builder`, `x-daisy-kit::table`,
`x-daisy-kit::tree`, `x-daisy-kit::blueprint`, `x-daisy-kit::file-preview`, and
`x-daisy-kit::map` are the complete public surface. Their contracts are documented in
[`docs/specs/v5-public-contract.md`](docs/specs/v5-public-contract.md).

## Explicit assets

Import only the JavaScript and CSS modules used on a host page. For example:

```js
import 'art35rennes/laravel-daisy-kit/dist/table.css';
import { mountAll } from 'art35rennes/laravel-daisy-kit/dist/table.js';

mountAll();
```

Each ESM entry exports `mount(root)`, `mountAll(scope = document)`, and `unmount(root)`.
There is no global bootstrap or `vendor:publish` step. Configuration is rendered as encoded
JSON, so a host can keep a strict CSP without inline script, handler, or style exceptions.

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
racing that server while recording every test in the graph. CI records a fresh, complete
baseline artifact. `composer test:full` remains the cache-independent release gate.

## Status

v5 is under active alpha validation. Existing v4 applications should remain on
[`v4.0.0`](https://github.com/art35rennes/laravel-daisy-kit/releases/tag/v4.0.0) or the
`legacy/4.x` branch until they choose to adopt the new API.
