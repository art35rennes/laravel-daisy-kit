# Laravel Daisy Kit v5

This repository is the source package for `art35rennes/laravel-daisy-kit` v5.

## Public surface

The only supported Blade components are listed in `docs/specs/v5-public-contract.md`.
Their namespace is `x-daisy-kit::`; do not add aliases, DaisyUI primitive wrappers,
templates, routes, or asset publishing.

## Frontend modules

Each public module owns independent ESM and CSS entries. Use the shared module helpers
in `resources/js/core/` and retain the `mount`, `mountAll`, `unmount` contract. Do not
introduce globals, inline handlers/styles/scripts, or cross-module implicit imports.

## Package conventions

- This is a Laravel package: use Testbench and Workbench rather than a root `artisan`.
- Livewire is optional at runtime. Never reference it outside a guarded integration.
- Keep `dist/` as intentionally tracked runtime output; do not track `node_modules` or
  Workbench build artefacts.
- Record changes to the public contract in `docs/decisions/` before implementation.
