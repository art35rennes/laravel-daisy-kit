# ADR-003: Resolve v5 assets through a host Vite alias

## Status

Accepted — 2026-08-26

## Context

Laravel Daisy Kit is installed through Composer/VCS, while its tracked `dist/` entries are built
for a host application's Vite pipeline. A Composer package name is not an npm package specifier,
so a bare import of the package name cannot be resolved by Vite.

## Decision

Hosts configure the stable Vite alias `@daisy-kit` to
`vendor/art35rennes/laravel-daisy-kit/dist` and import only explicit entries such as
`@daisy-kit/table.js` and `@daisy-kit/table.css`. The package continues to ship no Vite config,
asset publication, global bootstrap, or npm distribution.

## Consequences

- The host owns the one alias configuration alongside Tailwind CSS and DaisyUI.
- Documentation, Boost resources, and integration fixtures use the same copyable alias contract.
- The entry filenames remain part of the v5 public asset contract and are verified in a fresh
  Composer host fixture.
