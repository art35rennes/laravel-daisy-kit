# ADR-001: Rebuild Daisy Kit as a focused v5 package

## Status

Superseded by ADR-007 — 2026-08-29

## Context

Version 4 is a legacy release for existing integrations. Its broad component inventory,
global asset bundle, published assets, routes, and compatibility aliases make the
package hard to evolve safely. Version 5 is a deliberately incompatible new product.

## Decision

Version 5 uses the Laravel package skeleton conventions and supports PHP 8.4+ with
Illuminate 13 only. It exposes exactly seven Blade entries in the `daisy-kit` namespace:
Forms viewer, Forms builder, Table, Tree, Blueprint, File Preview, and Map.

Every module owns an explicit ESM and CSS entry below `dist/`. Applications import only
the modules they use through their own Vite build; the package neither publishes assets
nor starts a global browser bundle. Modules use semantic markup, encoded JSON
configuration, and DOM events named `daisy-kit:{module}:*` only.

The provider loads views and package configuration, reports its status in Laravel About,
and registers the Forms builder Livewire integration only when Livewire 4 is available.
It has no facade, routes, controllers, migrations, or publish groups.

## Consequences

- All `x-daisy` aliases, wrappers, templates, and legacy JavaScript are removed without
  adapters or a migration guide.
- Host applications own Tailwind and DaisyUI compilation.
- `dist/` is the only tracked generated runtime artifact; its deterministic build is
  verified before releases. Workbench build output and dependency directories stay ignored.
- The package remains installed from GitHub/VCS, not Packagist.

## Alternatives rejected

- Incremental compatibility layer: preserves the coupling v5 must remove.
- One combined JavaScript entry: conflicts with explicit, independently mountable modules.
- Bundling Tailwind or DaisyUI: duplicates host build ownership and increases conflicts.
