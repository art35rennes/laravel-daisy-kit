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
| Laravel Boost | 2.7.0 | Distributable agent guideline and package skill validation |

Runtime requires only `illuminate/support` and `illuminate/view` 13.x.

## JavaScript modules

| Package | Locked version | License | Module |
| --- | --- | --- | --- |
| @tanstack/table-core | 9.2.3 | MIT | Table |
| @tanstack/match-sorter-utils | 8.19.4 | MIT | Ranked local search for Combobox and Transfer List |
| signature_pad | 5.1.4 (`^5.1.3`) | MIT | Signature capture, point groups and PNG/SVG export |
| sortablejs | 1.15.7 | MIT | Optional Transfer List drag-and-drop ordering |
| @dagrejs/dagre | 3.1.1 | MIT | Blueprint |
| docx-preview | 0.4.0 | MIT | File Preview |
| leaflet | 1.9.4 | BSD-2-Clause | Map runtime |
| terra-draw / leaflet adapter | 1.32.3 / 1.3.0 | MIT | Map drawing |
| @turf/area / @turf/length / @turf/boolean-intersects | 7.4.0 | MIT | Map measurements and spatial selection |
| leaflet.markercluster / leaflet-gesture-handling | 1.5.3 / 1.2.2 | MIT | Optional Map clustering and gesture controls |
| Vite / Vitest | 8.2.2 / 4.1.11 | MIT | Reproducible module build and tests |
| jsdom | 30.0.1 | MIT | Browser-like unit-test environment |

Tailwind CSS and DaisyUI intentionally do not appear in the package bundle: the host owns
their installation and compilation. The reference development host uses DaisyUI `^5.7.22`.

SignaturePad and SortableJS write runtime DOM styles. Pages mounting Signature or Transfer List
must document and allow `style-src-attr 'unsafe-inline'`; all other modules retain the strict
`style-src-attr 'none'` parent-page policy.

## Major-version provenance

The registry lock files are the exact resolved provenance; the following official sources explain
the major decisions rather than replacing those locks:

| Dependency | Official source | Decision recorded here |
| --- | --- | --- |
| @tanstack/table-core 9.2.3 | [TanStack Table v9 migration guide](https://tanstack.com/table/latest/docs/framework/react/guide/migrating) and [feature guide](https://tanstack.com/table/latest/docs/guide/features) | Table registers only the v9 features and row models it uses; no v8 compatibility layer is retained. |
| jsdom 30.0.1 | [jsdom package metadata](https://github.com/jsdom/jsdom/blob/main/package.json) | Test-only; its Node engine requirement is met by the development and CI runtime. |
| Laravel Boost 2.7.0 | [Laravel Boost source and releases](https://github.com/laravel/boost) | The locked source commit is `b19e98a8637cb69b2aab7b5b6c5fe9e2c79d182f`; package-owned agent resources follow Boost 2.x terminology and discovery. |

ADR-005 records the upgrade process, source review, and the cache-independent validation rule.
