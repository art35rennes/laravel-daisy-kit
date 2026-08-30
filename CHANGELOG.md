# Changelog

## [Unreleased]

### Added

- Transfer List target ordering with Move up/down buttons and Alt+ArrowUp/ArrowDown, preserving focus and hidden/disabled item positions.
- Independent Copyable, Combobox, Signature, Truncate, Scrollspy, and Transfer List Blade/ESM/CSS modules.
- Stable module facades with `getInstance`, ranked selection search, signature export, and accessible non-drag interactions.
- Integrator facades for Tree, Blueprint, and File Preview, with stable identity across internal remounts.

### Changed

- The v5 public allowlist now contains exactly eleven modules and the reference host uses DaisyUI `^5.7.22`.
- Lifecycle commands, facade returns, structured errors, and public event payloads are now consistent and documented across all eleven modules.
- The Testbench Workbench is explicitly limited to a representative Laravel host; API documentation and facade diagnostics remain outside its visible UI.

### Removed

- Forms Viewer, Forms Builder, Livewire integration, and JSONata, without a compatibility layer.

## [5.0.0] - 2026-08-26

### Added

- Focused v5 Blade surface: Forms Viewer/Builder, Table, Tree, Blueprint, File Preview, and Map.
- Independently mountable ESM and CSS entries with strict-CSP configuration rendering.

### Removed

- All v4 components, aliases, global bundles, routes, asset publishing, templates, and legacy integrations.
