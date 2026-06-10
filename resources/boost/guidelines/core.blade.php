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
                ['name' => 'Revenue', 'data' => [12, 18, 24]],
            ]"
        />
    </x-daisy::ui.layout.hero>
</x-daisy::layout.app>

<x-daisy::templates.auth.login-simple />
</code-snippet>
@endverbatim

### Overrides

If a host app only needs a light visual or content adjustment, prefer published overrides instead of rebuilding equivalent UI from scratch:

- `daisy-views`
- `daisy-templates`
- `daisy-lang`

### Frontend Behavior

Before adding Alpine, vanilla JavaScript, or a new host-side widget, check whether the target package component already ships the required behavior through package assets, `window.DaisyKit`, or `data-module` hooks.

### Browser Autocomplete

- Treat the HTML `autocomplete` attribute as host-controlled markup, not package business policy.
- Keep semantic values in package-owned identity templates where the field meaning is known, such as auth, profile, password, one-time code, and contact fields.
- For business or sensitive host forms, prefer `autocomplete="off"` on the form/page template or on the specific field that needs it.
- For broad host conventions, use a host wrapper, layout, or published Daisy Kit override; do not add a global Daisy Kit switch that forces autocomplete behavior.
- Do not confuse browser/password-manager autocomplete with Daisy Kit remote autocomplete widgets such as enhanced selects or token inputs.

### CSP Compatibility

- Package components must work by default with `script-src 'self'` and `style-src 'self'`.
- Do not add inline event handlers, `style=""`, executable inline `<script>`, Alpine expression attributes such as `x-data` or `x-on:*`, `eval()`, or `new Function`.
- Prefer package classes for dynamic visual values. Use `data-*` attributes only when a package module can handle them without inline styles, and reserve nonceable server-side tags for documented exceptions.
- Static asset tags generated outside `@vite` should use the package nonce strategy via `daisy-kit.csp_nonce`.
- Custom theme CSS is disabled by default under strict CSP. Prefer build-time themes; if inline custom CSS is explicitly enabled, it must be nonceable.

### Conventions

- Keep reusable presentation in the package and business logic in the host application.
- Preserve concise public Blade usage such as `x-daisy::charts.line`, `x-daisy::ui.inputs.button`, and `x-daisy::templates.auth.login-simple`.
- Use `x-daisy::ui.layout.editable-grid` only for explicitly editable dashboards or builder-style surfaces; keep `x-daisy::ui.layout.grid-layout` as the default static grid.
- Treat reusing and composing package components as the default path, not the fallback.
