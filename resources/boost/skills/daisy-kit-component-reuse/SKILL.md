---
name: daisy-kit-component-reuse
description: "Use this skill when working in a Laravel application that uses art35rennes/laravel-daisy-kit and the task involves Blade UI, layouts, templates, CRUD pages, auth pages, tables, forms, feedback states, overlays, media, navigation, or interactive UI. The goal is to reuse existing Daisy Kit components, layouts, templates, overrides, and frontend behavior before creating new host-side markup."
license: MIT
metadata:
  author: art35rennes
---
# Daisy Kit Component Reuse

## Goal

Treat Laravel Daisy Kit as the default reusable UI inventory.

Do not rebuild a component, page shell, or layout that already exists in the package unless the requirement clearly exceeds the package surface.

## Lookup Order

1. Check `x-daisy::layout.*` for page shells and app chrome.
2. Check `x-daisy::ui.*` for reusable UI pieces.
3. Check `x-daisy::templates.*` and `daisy::templates.*` for full-page templates.
4. Check host overrides under `resources/views/vendor/daisy/...` for light package customizations.
5. Create new host-side Blade only if the package surface is insufficient.

## Reference Files

- Open `references/component-catalog.md` for a grouped, human-readable inventory.
- Open `references/components.json` for programmatic lookup by alias, group, path, and declared props.
- Those files are generated from the package Blade surface. In the package repository, regenerate them with `composer ai:catalog` after public UI changes.

## Public Entry Points By Intent

- Page shell or application chrome:
  - `x-daisy::layout.app`
  - `x-daisy::layout.sidebar-layout`
  - `x-daisy::layout.navbar-layout`
  - `x-daisy::layout.navbar-sidebar-layout`
- Authentication and account flows:
  - `x-daisy::templates.auth.*`
  - `x-daisy::templates.profile.*`
- Forms and input handling:
  - `x-daisy::ui.partials.form-field`
  - `x-daisy::ui.inputs.*`
  - `x-daisy::templates.form.*`
- CRUD and structured data:
  - `x-daisy::ui.layout.crud-layout`
  - `x-daisy::ui.layout.crud-section`
  - `x-daisy::ui.data-display.table`
- Navigation, sections, and page structure:
  - `x-daisy::ui.navigation.*`
  - `x-daisy::ui.layout.*`
- Feedback, empty, loading, and error states:
  - `x-daisy::ui.feedback.*`
  - `x-daisy::ui.errors.*`
  - `x-daisy::templates.error`
  - `x-daisy::templates.empty-state`
  - `x-daisy::templates.loading-state`
  - `x-daisy::templates.maintenance`
- Overlays and disclosure UI:
  - `x-daisy::ui.overlay.*`
  - `x-daisy::ui.advanced.accordion`
  - `x-daisy::ui.advanced.collapse`
- Media and rich interactive surfaces:
  - `x-daisy::ui.media.*`
  - `x-daisy::ui.advanced.*`
  - `x-daisy::ui.utilities.*`
- Chat, notifications, and communication UI:
  - `x-daisy::ui.communication.*`

## Reuse Rules

- Prefer composition over copying package markup into a host Blade file.
- Prefer vendor overrides over host-local clones when the change is mostly visual, structural, or textual.
- Before adding new JavaScript, check whether the target package component already ships a package module, a `data-module` hook, or a package entrypoint that covers the behavior.
- Keep host-specific business logic in the host application and reusable presentation in the package.
- If you create a new host component anyway, explain why the package aliases and overrides were not sufficient.

## Verification

- Confirm that the alias you plan to use exists in `references/component-catalog.md` or `references/components.json`.
- If you add, rename, or remove a public package component or template, regenerate the catalog and commit the updated references.
