# v5 dependency provenance

Dependencies are resolved from their official Composer and npm registries on 2026-08-27,
using stable versions compatible with PHP 8.4 and Laravel 13. `composer.lock` and
`package-lock.json` are the authoritative, reproducible resolution records.

Pest 5 requires PHP 8.4+, so the package, contributors, and CI all use PHP 8.4 with PCOV.

## PHP development toolchain

| Package | Locked version | Purpose |
| --- | --- | --- |
| Laravel Framework | 13.29.0 | Laravel 13 integration and Workbench host |
| Orchestra Testbench | 11.2.0 | Package testing and Workbench |
| Pest | 5.1.3 | Test runner |
| Pest Laravel / type coverage | 5.0.1 / 5.0.2 | Laravel integration and strict type coverage |
| Pest Browser / Playwright | 5.0.1 / 1.62.1 | Workbench keyboard, focus, and responsive browser smoke tests |
| Larastan | 3.10.0 | Static analysis at max level |
| Laravel Pint | 1.30.5 | PHP formatting |
| Livewire | 4.4.2 | Development-only test of optional Forms Builder enhancement |
| Laravel Boost | 2.7.0 | Distributable agent guideline and package skill validation |

Runtime requires only `illuminate/support` and `illuminate/view` 13.x. Livewire remains a
Composer suggestion, never a runtime requirement.

## JavaScript modules

| Package | Locked version | Module |
| --- | --- | --- |
| jsonata | 2.2.2 | Forms Viewer |
| @tanstack/table-core | 9.2.3 | Table |
| @dagrejs/dagre | 3.1.1 | Blueprint |
| docx-preview | 0.4.0 | File Preview |
| leaflet | 1.9.4 | Map runtime |
| terra-draw / leaflet adapter | 1.32.3 / 1.3.0 | Map drawing |
| @turf/area / @turf/length | 7.4.0 / 7.4.0 | Map measurements |
| Vite / Vitest | 8.2.2 / 4.1.11 | Reproducible module build and tests |
| jsdom | 30.0.1 | Browser-like unit-test environment |

Tailwind CSS and DaisyUI intentionally do not appear in the package bundle: the host owns
their installation and compilation.

## Major-version provenance

The registry lock files are the exact resolved provenance; the following official sources explain
the major decisions rather than replacing those locks:

| Dependency | Official source | Decision recorded here |
| --- | --- | --- |
| @tanstack/table-core 9.2.3 | [TanStack Table v9 migration guide](https://tanstack.com/table/latest/docs/framework/react/guide/migrating) and [feature guide](https://tanstack.com/table/latest/docs/guide/features) | Table registers only the v9 features and row models it uses; no v8 compatibility layer is retained. |
| jsdom 30.0.1 | [jsdom package metadata](https://github.com/jsdom/jsdom/blob/main/package.json) | Test-only; its Node engine requirement is met by the development and CI runtime. |
| Laravel Boost 2.7.0 | [Laravel Boost source and releases](https://github.com/laravel/boost) | The locked source commit is `b19e98a8637cb69b2aab7b5b6c5fe9e2c79d182f`; package-owned agent resources follow Boost 2.x terminology and discovery. |

ADR-005 records the upgrade process, source review, and the cache-independent validation rule.
