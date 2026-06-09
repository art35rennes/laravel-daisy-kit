# Changelog

All notable changes to this package are documented in this file.

This project follows [Semantic Versioning 2.0.0](https://semver.org/lang/fr/).

## [3.0.0] - 2026-06-09

### Added

- Add Daisy Form Kit with a JSON-driven `x-daisy::forms.viewer`, Livewire-powered `x-daisy::forms.builder`, canonical schema export, diagnostics, nested field ordering, responsive authoring UI, and host-owned persistence/submission boundaries.
- Add Form Kit PHP helpers for schema normalization, schema validation, Laravel rule mapping, error bag mapping, JSONata evaluation contracts, and server-side submission normalization.
- Expose the viewer JavaScript runtime through identifiable form roots, `window.DaisyFormViewer`, runtime events, value APIs, validation APIs, and programmatic field updates.
- Add reusable chart components and chart runtime modules for line, area, bar, stacked, pie, donut, and sparkline variants.
- Add choice card group, editable grid, ordered list, richer breadcrumb/pagination/navigation rendering, and Laravel-aware form conveniences.
- Add distributable package assets under `dist/vendor/art35rennes/laravel-daisy-kit/` so host apps can publish package assets without building the package locally.
- Add bundled agent and component catalog guidance for Daisy Kit component reuse.

### Changed

- Replaced the DataTables/jQuery-based datatable implementation with a new `x-daisy::ui.data-display.table` component powered by `@tanstack/table-core`.
- Removed the public DataTables options surface in favor of a smaller Blade API built around `rows`, `mode`, `endpoint`, `method`, and `initialState`.
- Switched the auto-bootstrapped runtime from `window.DaisyDataTable` to `window.DaisyTable`.
- Removed the package DataTables CSS/theme layer and shipped a minimal DaisyUI-native table stylesheet instead.
- Added V2 table server adapters, including an explicit `spatie-query-builder` mode with adapter-native query serialization.
- Added real column filter controls, state persistence (`url` / `local`), and Laravel paginator response normalization for server-backed tables.
- Refined public template APIs for auth, profile, errors, changelog, communication, and layout templates, with stricter component URL and route handling.
- Improved color picker, code editor, transfer, sidebar, modal, alert, and viewer runtime behavior for nested/package usage.

### Fixed

- Viewer submissions preserve HTTP methods, support multipart file payloads, normalize field aliases to submit names, map Laravel 422 JSON errors, and keep recovered expression diagnostics clean.
- Form viewer runtime keeps color picker dropdown interactions internal, prunes stale runtimes, applies visibility to containers, and excludes static text from submitted values.
- Form builder authoring keeps Livewire as the single schema source, filters no-op drop zones, supports selected-object insertion, and keeps preview rendering through the real viewer.
- Alert component no longer renders an empty container when `sessionKey` or `text` is blank.
- Dismissible alerts use `data-module="alert-dismiss"` instead of inline `onclick`, so they work under strict CSP.
- Fix auth label layout when templates provide rich `labelSlot` content (for example the login password row with a forgot-password link).
- Hide self-service signup messaging on login templates unless a `register` route exists or `showSignup` is enabled.
- Restore the `reset-password` template as a real password reset form instead of a duplicate forgot-password screen.

### Breaking

- `x-daisy::ui.data-display.datatable` no longer works as a public component and now raises an explicit migration error.
- DataTables-compatible server endpoints are no longer supported; server mode now uses the package JSON contract (`pageIndex`, `pageSize`, `sorting`, `globalFilter`, `columnFilters`, `columnVisibility`).
- DataTables-specific props such as `ajax`, `options`, `responsive`, `layout`, `pageLength`, `ordering`, `language`, and `scrollX` have been removed.
- Direct use of removed internal component-template view paths under `daisy::components.templates.*` must migrate to the public `x-daisy::templates.*` / `daisy::templates.*` surfaces.
- Host applications that depended on inline alert dismiss handlers must load the package JS assets and use the `data-module="alert-dismiss"` runtime.

## [1.0.0] - 2026-04-12

Initial stable release.

### Added

- Laravel package structure for reusable Blade UI components and templates.
- `daisy::` Blade namespace for UI components and layout helpers.
- `daisy::templates.*` views for auth, profile, form, communication, error, and changelog pages.
- French and English translation files under the `daisy::` namespace.
- Optional published frontend assets for DaisyUI 5 and Tailwind CSS 4 integration.
- Package tests covering rendering, helpers, assets, service provider behavior, and JavaScript modules.

[3.0.0]: https://github.com/art35rennes/laravel-daisy-kit/compare/v2.0.1...v3.0.0
[1.0.0]: https://github.com/art35rennes/laravel-daisy-kit/releases/tag/v1.0.0
