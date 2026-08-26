# Laravel Daisy Kit v5

This repository is the source package for `art35rennes/laravel-daisy-kit` v5.

## Public surface

The only supported Blade components are listed in `docs/specs/v5-public-contract.md`.
Their namespace is `x-daisy-kit::`; do not add aliases, DaisyUI primitive wrappers,
templates, routes, or asset publishing.
`docs/specs/v5-product-contract-matrix.md` is the single business-contract oracle for
those entries: restore its differentiated user outcomes with an outcome test rather than
validating a mount state alone.

## Frontend modules

Each public module owns independent ESM and CSS entries. Use the shared module helpers
in `resources/js/core/` and retain the `mount`, `mountAll`, `unmount` contract. Do not
introduce globals, inline handlers/styles/scripts, or cross-module implicit imports.

Consumers configure Vite's stable `@daisy-kit` alias to
`vendor/art35rennes/laravel-daisy-kit/dist`, then import explicit entries such as
`@daisy-kit/table.js` and `@daisy-kit/table.css`. This is a Composer/VCS package, never an npm
specifier.

## Package conventions

- This is a Laravel package: use Testbench and Workbench rather than a root `artisan`.
- Livewire is optional at runtime. Never reference it outside a guarded integration.
- Keep `dist/` as intentionally tracked runtime output; do not track `node_modules` or
  Workbench build artefacts.
- Record changes to the public contract in `docs/decisions/` before implementation.

## Agent resources

- The package ships concise Laravel Boost resources in `resources/boost/`. Keep the
  guideline foundational and place detailed package instructions only in its on-demand
  skill; do not copy Laravel's generated guidance into this repository.
- In a consuming Laravel application with Boost 2.7+, install positive capabilities with
  `php artisan boost:install --guidelines --skills --mcp`, then use
  `php artisan boost:update --discover` after adding or updating packages. These are host
  commands, not package-root commands.
- Boost-generated agent outputs and caches (`.ai/`, `.agents/`, `.codex/`, `boost.json`,
  and `CLAUDE.md`) are local state. Version the package-owned sources above and this
  repository convention file instead.
- When Boost exposes `laravel-best-practices`, activate it for Laravel PHP work; this
  repository-specific skill complements it and does not restate generic Laravel rules.
