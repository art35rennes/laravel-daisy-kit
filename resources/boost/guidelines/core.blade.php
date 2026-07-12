## Laravel Daisy Kit

This package is the default reusable UI layer when it is installed in a host Laravel application.

### Reuse Order

- Check the package surface before writing new host-side Blade:
  - `x-daisy::layout.*`
  - `x-daisy::charts.*`
  - `x-daisy::ui.*`
  - `x-daisy::templates.*`
  - published overrides in `resources/views/vendor/daisy/...`
- Compose existing package components before creating a host component or layout that duplicates them.
- Create a new host-side Blade component only when the package surface and vendor overrides cannot cover the requirement cleanly.
- For operations dashboards with KPI cards, filters, charts, and quick actions, start from `x-daisy::templates.reporting.operations-dashboard` before composing a host-only reporting page.

### Skill

If the `daisy-kit-component-reuse` skill is available, load it before building or refactoring Blade UI that might overlap with this package.

That skill contains:

- a generated catalog of the public component and template surface
- package entry points grouped by intent
- override and composition rules
- reuse checks for interactive package behavior

### Blade Usage

Use the package namespace and aliases directly:

@verbatim
<code-snippet name="Render Daisy Kit components and templates" lang="blade">
<x-daisy::layout.app title="Dashboard">
    <x-daisy::ui.layout.hero title="Overview">
        <x-daisy::ui.feedback.alert color="info" title="Heads up">
            Existing package components should be reused first.
        </x-daisy::ui.feedback.alert>
        <x-daisy::charts.line
            title="Revenue"
            :categories="['Jan', 'Feb', 'Mar']"
            :series="[
                ['name' => 'Revenue', 'data' => [
                    ['value' => 12, 'drilldown' => ['month' => 'jan']],
                    ['value' => 18, 'drilldown' => ['month' => 'feb']],
                    ['value' => 24, 'drilldown' => ['month' => 'mar']],
                ]],
            ]"
            drilldown-url="/reports"
            :drilldown-params="['chart' => 'revenue']"
            :markers="[['type' => 'line', 'value' => 20, 'name' => 'Target']]"
        />
    </x-daisy::ui.layout.hero>
</x-daisy::layout.app>

<x-daisy::templates.auth.login-simple />
<x-daisy::templates.reporting.operations-dashboard />
</code-snippet>
@endverbatim

### Overrides

If a host app only needs a light visual or content adjustment, prefer published overrides instead of rebuilding equivalent UI from scratch:

- `daisy-views`
- `daisy-templates`
- `daisy-lang`

### Frontend Behavior

Before adding Alpine, vanilla JavaScript, or a new host-side widget, check whether the target package component already ships the required behavior through package assets, `window.DaisyKit`, or `data-module` hooks.

Use `window.DaisyKit.notify(...)`, the `daisy:notify` event, and a single triggerable `x-daisy::ui.feedback.toast` container for on-demand toast notifications with actions and auto-dismiss behavior. Do not add host-side toast scripts for standard business feedback. Keep critical destructive confirmations on `x-daisy::ui.overlay.popconfirm` or modal flows instead of toast actions.

For hierarchical form choices, use `x-daisy::ui.advanced.tree-view`. Keep node data structural (`id`, `label`, `children`, `disabled`, `lazy`, `expanded`) and pass selected IDs through `value`; do not put `selected`, `checked`, or custom key aliases on nodes. Multiple selection cascades through parents and submits selected leaves only.

For DaisyUI 5.6 surfaces, prefer `x-daisy::ui.inputs.otp`, `x-daisy::ui.advanced.aura`, and `x-daisy::ui.navigation.megamenu`. Use the existing range, tooltip, modal, card, and choice-card components for vertical sliders, aligned tooltips, Popover API modals, and selectable cards instead of recreating their markup.

For directed business workflows, prefer `x-daisy::ui.advanced.blueprint` and its `value`, `nodeCategories`, and `transitionCategories` contract before creating a custom graph editor. Use `layout="hierarchical|tree|radial"`, `transitionShape="straight|curve|s|orthogonal"`, and DaisyUI semantic `nodeColor`/`transitionColor` values for host-controlled presentation; node categories may override `color`, while transition categories may override `shape` and `color`, without changing persisted workflow data.

### Browser Autocomplete

- Treat the HTML `autocomplete` attribute as host-controlled markup, not package business policy.
- Keep semantic values in package-owned identity templates where the field meaning is known, such as auth, profile, password, one-time code, and contact fields.
- For business or sensitive host forms, prefer `autocomplete="off"` on the form/page template or on the specific field that needs it.
- For broad host conventions, use a host wrapper, layout, or published Daisy Kit override; do not add a global Daisy Kit switch that forces autocomplete behavior.
- Do not confuse browser/password-manager autocomplete with Daisy Kit remote autocomplete widgets such as enhanced selects, multi-selects, or token inputs.

### CSP Compatibility

- Package components must work by default with `script-src 'self'` and `style-src 'self'`.
- Do not add inline event handlers, `style=""`, executable inline `<script>`, Alpine expression attributes such as `x-data` or `x-on:*`, `eval()`, or `new Function`.
- Prefer package classes for dynamic visual values. Use `data-*` attributes only when a package module can handle them without inline styles, and reserve nonceable server-side tags for documented exceptions.
- Static asset tags generated outside `@vite` should use the package nonce strategy via `daisy-kit.csp_nonce`.
- Treat CSP nonces as authorization for nonce-bearing `<script>` and `<style>` tags only; a nonce does not make `style=""`, `element.style.*`, inline event attributes, or string evaluation acceptable.
- When using headless libraries such as TanStack Table, delegate state and behavior to the library but keep Daisy Kit responsible for CSP-safe markup. Do not copy framework examples that apply sizing, transforms, or other dynamic visual values through inline styles unless the component is explicitly documented as requiring a relaxed CSP.
- Custom theme CSS is disabled by default under strict CSP. Prefer build-time themes; if inline custom CSS is explicitly enabled, it must be nonceable.

### Conventions

- Keep reusable presentation in the package and business logic in the host application.
- Preserve concise public Blade usage such as `x-daisy::charts.line`, `x-daisy::ui.inputs.button`, and `x-daisy::templates.auth.login-simple`.
- For reporting charts, use `x-daisy::charts.*` with the default SVG renderer, enriched point actions, `markers`, `zoom`, and `orientation="horizontal"` before writing bespoke SVG, canvas, or host-side chart markup. Keep `drilldownUrl` for filtered navigation. For a packaged modal target, use point `action: ['type' => 'event', 'intent' => 'detail', 'target' => '#detail-modal']`; the chart populates `[data-chart-detail-name]`, `[data-chart-detail-value]`, `[data-chart-detail-series]`, and `[data-chart-detail-link]` inside the target before opening it. Listen for the cancelable `daisy:chart-activate` event when the host needs custom business behavior instead.
- Use `x-daisy::ui.partials.form-field` as the default wrapper for label + input/select pairs, especially in constrained grids. Keep host CSS from targeting package `.label`, `.input`, or `.select` just to fix field alignment.
- Use `x-daisy::ui.layout.editable-grid` only for explicitly editable dashboards or builder-style surfaces; keep `x-daisy::ui.layout.grid-layout` as the default static grid.
- Use `x-daisy::ui.utilities.copyable` for long technical values such as checksums, UUIDs, masked tokens, and generated identifiers. Prefer `class="font-mono break-all"`, `icon-position="inline"`, configurable `success-message`, and `:underline="false"` when the value sits inside a dense detail view.
- Use `x-daisy::ui.inputs.icon-button` for icon-only actions that need a stable DaisyUI circular button with `aria-label`, `title`, and tooltip text.
- Use `x-daisy::ui.data-display.description-list` for structured key/value detail panels before writing host-side `<dl>` grids.
- Treat reusing and composing package components as the default path, not the fallback.
