# v5 dependency provenance

Dependencies are resolved from their official Composer and npm registries on 2026-08-26,
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

Runtime requires only `illuminate/support` and `illuminate/view` 13.x. Livewire remains a
Composer suggestion, never a runtime requirement.

## JavaScript modules

| Package | Locked version | Module |
| --- | --- | --- |
| jsonata | 2.2.1 | Forms Viewer |
| @tanstack/table-core | 8.21.3 | Table |
| @dagrejs/dagre | 3.0.0 | Blueprint |
| docx-preview | 0.4.0 | File Preview |
| leaflet | 1.9.4 | Map runtime |
| terra-draw / leaflet adapter | 1.32.0 / 1.3.0 | Map drawing |
| @turf/area / @turf/length | 7.3.5 / 7.3.5 | Map measurements |
| Vite / Vitest | 8.1.4 / 4.1.11 | Reproducible module build and tests |

Tailwind CSS and DaisyUI intentionally do not appear in the package bundle: the host owns
their installation and compilation.
