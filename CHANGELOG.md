# Changelog

## [Unreleased]

## [6.1.0] - 2026-09-18

### Added

- Add the independent Code Editor Blade, ESM and CSS module with native form semantics, lazy CodeMirror grammars, localized controls, folding, completion and local on-demand formatting.
- Add the independent Trix WYSIWYG Blade, ESM and CSS module for v6.1.0, with native forms, lifecycle, browser sanitization, host-coordinated attachments, CSP guidance and a public `getTrixEditor()` escape hatch.

### Changed

- Expand the supported v6 public surface from eleven to thirteen independent modules and update the package Boost guidance and fresh Vite host coverage.

### Fixed

- Keep Scrollspy navigation within its configured scrollable content panel.
- Preserve asynchronous editor state across formatting, language loading and WYSIWYG host synchronization.

## [6.0.1] - 2026-09-13

### Fixed

- Preserve unsaved Table cell text when a pending search completes and rerenders the editor. The immutable v6.0.0 tag is affected; v6.0.1 is the first public stable GitHub release of the v6 line.

## [6.0.0] - 2026-09-13

### Breaking release

- New major version because Forms Viewer/Builder and the Livewire integration from v5.0.0 are removed. No compatibility layer is provided.
- Stable distribution through GitHub/VCS; require `^6.0` with Composer and the documented Vite alias.

### Fixed

- Preserve imported signatures through resize and undo, and cancel pending imports on clear, new strokes and unmount.
- Build Testbench host assets before Feature tests so clean installations do not rely on local manifests.

### Added

- Optional Copyable icon and transient visual success/error feedback backed by the existing accessible live status.
- Restored Tree hierarchical selector with initial values, cascade and selected-root modes,
  visible/hidden counters, manual or fuzzy search, lazy retry, custom inert Blade nodes,
  English/French labels, expanded integration API and four realistic Workbench scenarios.
- Tree host filters, optional match highlighting and cursor-paginated lazy branches, with a
  successful-by-default Workbench catalogue and corrected visible-result selection.
- Transfer List assignment panels with rich safe rows, scoped select-all, independent local
  pagination, one-way operation, selection counts and explicit empty/search states.
- Combobox open-on-focus suggestions, integrated tokens, bounded rich person/e-mail options, and a
  CSP-safe host option renderer.
- Combobox client option replacement, configurable canonical search fields, viewport-aware overlay
  placement, and selected-label preservation across partial or empty remote results.
- Transfer List target ordering with Move up/down buttons and Alt+ArrowUp/ArrowDown, preserving focus and hidden/disabled item positions.
- Independent Copyable, Combobox, Signature, Truncate, Scrollspy, and Transfer List Blade/ESM/CSS modules.
- Stable module facades with `getInstance`, ranked selection search, signature export, and accessible non-drag interactions.
- Integrator facades for Tree, Blueprint, and File Preview, with stable identity across internal remounts.

### Changed

- The v6 public allowlist now contains exactly eleven modules and the reference host uses DaisyUI `^5.7.22`.
- Lifecycle commands, facade returns, structured errors, and public event payloads are now consistent and documented across all eleven modules.
- The Testbench Workbench is explicitly limited to a representative Laravel host; API documentation and facade diagnostics remain outside its visible UI.
- Truncate now uses an anchored compact ellipsis preview, temporary hover/focus disclosure, pinned selectable text, native light dismiss, and an optional pinned-state backdrop.
- Truncate keeps its ellipsis adjacent to clipped text and presents its native popover as a
  responsive DaisyUI card, including a viewport-contained mobile panel.
- Transfer List uses an unambiguous square multi-selection affordance, substring-ranked search and
  filtered-versus-total result counts.
- The Workbench demonstrates explicit, visible-text, structured-text and disabled Copyable cases,
  plus local and server-backed Combobox forms with multi-field e-mail-domain search.

### Removed

- Forms Viewer, Forms Builder, Livewire integration, and JSONata, without a compatibility layer.

## [5.0.0] - 2026-08-26

### Added

- Focused v5 Blade surface: Forms Viewer/Builder, Table, Tree, Blueprint, File Preview, and Map.
- Independently mountable ESM and CSS entries with strict-CSP configuration rendering.

### Removed

- All v4 components, aliases, global bundles, routes, asset publishing, templates, and legacy integrations.
