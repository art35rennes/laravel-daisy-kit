# v6 dependency provenance

Dependencies are resolved from their official Composer and npm registries on 2026-10-24,
using stable versions compatible with PHP 8.4 and Laravel 13. `composer.lock` and
`package-lock.json` are the authoritative, reproducible resolution records.

The runtime security floor is PHP 8.4.27; PHP 8.5 requires 8.5.12+. CI covers both branches with PCOV. Node builds use 24.22.1 (see `.nvmrc`).

## PHP development toolchain

| Package | Locked version | Purpose |
| --- | --- | --- |
| Laravel Framework | 13.38.0 | Laravel 13 integration and Workbench host |
| Orchestra Testbench | 11.3.0 | Package testing and Workbench |
| Pest | 5.3.3 | Test runner |
| Pest Laravel / type coverage | 5.0.1 / 5.0.3 | Laravel integration and strict type coverage |
| Pest Browser / Playwright | 5.1.2 / 1.64.1 | Workbench keyboard, focus, and responsive browser smoke tests |
| Larastan | 3.13.2 | Static analysis at max level |
| Laravel Pint | 1.32.1 | PHP formatting |
| Laravel Boost | 2.10.3 | Distributable agent guideline and package skill validation |

Runtime requires only `illuminate/support` and `illuminate/view` 13.x.

## JavaScript modules

| Package | Locked version | License | Module |
| --- | --- | --- | --- |
| @tanstack/table-core | 9.3.1 | MIT | Table |
| @tanstack/match-sorter-utils | 8.19.4 | MIT | Ranked local search for Combobox and Transfer List |
| signature_pad | 5.1.4 (`^5.1.3`) | MIT | Signature capture, point groups and PNG/SVG export |
| sortablejs | 1.15.7 | MIT | Optional Transfer List drag-and-drop ordering |
| @dagrejs/dagre | 3.1.2 | MIT | Blueprint |
| docx-preview | 0.4.1 | MIT | File Preview |
| pdfjs-dist | 6.4.299 | Apache-2.0 | Sandboxed PDF rendering for File Preview |
| leaflet | 1.9.4 | BSD-2-Clause | Map runtime |
| terra-draw / leaflet adapter | 1.38.0 / 1.4.0 | MIT | Map drawing |
| @turf/area / @turf/length / @turf/boolean-intersects | 7.4.0 | MIT | Map measurements and spatial selection |
| leaflet.markercluster / leaflet-gesture-handling | 1.5.3 / 1.2.2 | MIT | Optional Map clustering and gesture controls |
| Trix | 2.1.19 (`^2.1.19`) | MIT | WYSIWYG editing, bundled DOMPurify sanitization and attachment model |
| Prettier | 3.9.10 | MIT | Code Editor formatting, loaded on demand with the matching web-language parser |
| @prettier/plugin-php | 0.25.0 | MIT | On-demand PHP formatting in Code Editor |
| sql-formatter | 15.9.0 | MIT | On-demand SQL formatting in Code Editor |
| Vite / Vitest | 8.3.5 / 4.1.12 | MIT | Reproducible module build and tests |
| jsdom | 30.1.3 | MIT | Browser-like unit-test environment |

Tailwind CSS and DaisyUI intentionally do not appear in the package bundle: the host owns
their installation and compilation. The reference development host uses DaisyUI `^5.7.22`.

SignaturePad, SortableJS and Trix write runtime DOM styles. Pages mounting Signature,
Transfer List or WYSIWYG must document and allow `style-src-attr 'unsafe-inline'`.
WYSIWYG pages also authorize the Trix style nonce in `style-src` and allow `img-src blob:`
when local attachment previews are enabled. All other modules retain the strict
`style-src-attr 'none'` parent-page policy.

## Major-version provenance

The registry lock files are the exact resolved provenance; the following official sources explain
the major decisions rather than replacing those locks:

| Dependency | Official source | Decision recorded here |
| --- | --- | --- |
| @tanstack/table-core 9.3.1 | [TanStack Table v9 migration guide](https://tanstack.com/table/latest/docs/framework/react/guide/migrating) and [feature guide](https://tanstack.com/table/latest/docs/guide/features) | Table registers only the v9 features and row models it uses; no v8 compatibility layer is retained. |
| jsdom 30.1.3 | [jsdom package metadata](https://github.com/jsdom/jsdom/blob/main/package.json) | Test-only; its Node engine requirement is met by the development and CI runtime. |
| Laravel Boost 2.10.3 | [Laravel Boost source and releases](https://github.com/laravel/boost) | The locked source commit is `75c269a8a53e931c7252e7cd562e1a209356a7c6`; package-owned agent resources follow Boost 2.x terminology and discovery. |

ADR-005 records the upgrade process, source review, and the cache-independent validation rule.

Code Editor adds the MIT-licensed CodeMirror 6 state, view, commands, language,
search and autocomplete packages, @lezer/highlight, and official PHP, HTML, CSS,
JavaScript/TypeScript, JSON, Markdown, SQL and YAML grammars. Exact versions are
locked in package-lock.json. Consumers use compiled assets without npm installation.

Code Editor also bundles Prettier standalone and its official web-language plugins
as lazy chunks. Formatting JSON, JavaScript/TypeScript, HTML, CSS, Markdown and YAML
runs locally in the browser, with no server request. PHP uses the official Prettier PHP
plugin and SQL uses sql-formatter. Plain text formatting is not included. Formatters
and their parser chunks load only when formatting is requested.

The October security maintenance and pending migrations are recorded in [maintenance-2026-10-24.md](maintenance-2026-10-24.md).
