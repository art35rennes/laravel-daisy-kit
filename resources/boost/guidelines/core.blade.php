## Laravel Daisy Kit

This package is the default reusable UI layer when it is installed in a host Laravel application.

### Reuse Order

- Check the package surface before writing new host-side Blade:
  - `x-daisy::layout.*`
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

### Conventions

- Keep reusable presentation in the package and business logic in the host application.
- Preserve concise public Blade usage such as `x-daisy::ui.inputs.button` and `x-daisy::templates.auth.login-simple`.
- Treat reusing and composing package components as the default path, not the fallback.
