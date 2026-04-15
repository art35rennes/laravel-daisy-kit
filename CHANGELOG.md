# Changelog

All notable changes to this package are documented in this file.

This project follows [Semantic Versioning 2.0.0](https://semver.org/lang/fr/).

## [Unreleased]

### Changed

- Replaced the DataTables/jQuery-based datatable implementation with a new `x-daisy::ui.data-display.table` component powered by `@tanstack/table-core`.
- Removed the public DataTables options surface in favor of a smaller Blade API built around `rows`, `mode`, `endpoint`, `method`, and `initialState`.
- Switched the auto-bootstrapped runtime from `window.DaisyDataTable` to `window.DaisyTable`.
- Removed the package DataTables CSS/theme layer and shipped a minimal DaisyUI-native table stylesheet instead.
- Added V2 table server adapters, including an explicit `spatie-query-builder` mode with adapter-native query serialization.
- Added real column filter controls, state persistence (`url` / `local`), and Laravel paginator response normalization for server-backed tables.

### Breaking

- `x-daisy::ui.data-display.datatable` no longer works as a public component and now raises an explicit migration error.
- DataTables-compatible server endpoints are no longer supported; server mode now uses the package JSON contract (`pageIndex`, `pageSize`, `sorting`, `globalFilter`, `columnFilters`, `columnVisibility`).
- DataTables-specific props such as `ajax`, `options`, `responsive`, `layout`, `pageLength`, `ordering`, `language`, and `scrollX` have been removed.

## [1.0.0] - 2026-04-12

Initial stable release.

### Added

- Laravel package structure for reusable Blade UI components and templates.
- `daisy::` Blade namespace for UI components and layout helpers.
- `daisy::templates.*` views for auth, profile, form, communication, error, and changelog pages.
- French and English translation files under the `daisy::` namespace.
- Optional published frontend assets for DaisyUI 5 and Tailwind CSS 4 integration.
- Package tests covering rendering, helpers, assets, service provider behavior, and JavaScript modules.

[1.0.0]: https://github.com/art35rennes/laravel-daisy-kit/releases/tag/v1.0.0
