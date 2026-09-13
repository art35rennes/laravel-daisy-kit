# ADR-005: Major dependency upgrades are product migrations

## Status

Accepted — 2026-08-27

## Decision

Direct Composer and npm dependencies are audited after the functional and browser
gates, then upgraded to the newest stable release compatible with PHP 8.4,
Laravel 13, and the package's explicit-host-build architecture. Lock files record
the resolved provenance. Every major is a separate RED-to-GREEN slice; the release
suite remains runnable with no TIA cache.

The first current major is `@tanstack/table-core` 9. Its v8-style APIs and its
deprecated legacy migration path are not permitted in this v5 package. The Table
module must instead register exactly its used v9 features and row models with the
framework-neutral core. This preserves filtering, sorting, pagination, visibility,
pinning, and selection while keeping unused features out of the bundle.

`jsdom` 30 requires Node `^22.22.2 || ^24.15.0 || >=26`; the current development
runtime satisfies that constraint. It remains a test-only dependency.

## Sources

- [TanStack Table v9 migration guide](https://tanstack.com/table/latest/docs/framework/react/guide/migrating)
- [TanStack Table v9 feature guide](https://tanstack.com/table/latest/docs/guide/features)
- [jsdom 30 package metadata](https://github.com/jsdom/jsdom/blob/main/package.json)
