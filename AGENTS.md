# Laravel Daisy Kit

This repository is the source package for `art35rennes/laravel-daisy-kit`.

## Reuse First

- Treat Daisy Kit as the default reusable UI system.
- Before adding or rewriting Blade UI, check:
  - `resources/boost/skills/daisy-kit-component-reuse/references/component-catalog.md`
  - `resources/boost/skills/daisy-kit-component-reuse/references/components.json`
  - `resources/views/components`
  - `resources/views/templates`
- Prefer existing `x-daisy::layout.*`, `x-daisy::ui.*`, and `x-daisy::templates.*` aliases before creating new package or host-side Blade files.
- Compose existing package components before creating a new one.
- In host applications, prefer published overrides under `resources/views/vendor/daisy/...` when only light visual or markup adjustments are needed.
- Reuse existing package JavaScript behavior before adding parallel host-side interactivity.

## Public UI Surface

- Blade namespace: `daisy::`
- Component aliases: `x-daisy::...`
- Template aliases and views: `x-daisy::templates.*` and `daisy::templates.*`
- Primary discovery reference: `resources/boost/skills/daisy-kit-component-reuse/references/component-catalog.md`

## When The Surface Changes

- If you add, rename, or remove a public component, layout, or template, run `composer ai:catalog`.
- Keep `resources/boost/guidelines/core.blade.php` and the `daisy-kit-component-reuse` skill aligned with the current package surface.
- Do not let AI guidance drift away from the actual Blade API.
