## Laravel Daisy Kit

This package provides reusable Blade UI components, page templates, translations, and frontend assets for Laravel applications built with DaisyUI and Tailwind CSS.

### Integration Priority

When working in a host Laravel application that uses this package:

- Prefer existing `x-daisy::...` Blade components before creating new host-side Blade components.
- Prefer existing `x-daisy::templates...` page templates before building a new page from scratch.
- Prefer composing package components into host pages instead of copying package markup into custom Blade files.
- If the host app only needs a light visual or content adjustment, prefer published overrides under `resources/views/vendor/daisy/...`.
- Create new host Blade components or pages only when the package components, templates, and overrides do not cover the need cleanly.

### Blade Usage

Use the package Blade namespace:

@verbatim
<code-snippet name="Render Daisy Kit components and templates" lang="blade">
<x-daisy::ui.inputs.button color="primary">
    Save
</x-daisy::ui.inputs.button>

<x-daisy::ui.feedback.alert color="success" title="Profile updated">
    Your changes have been saved.
</x-daisy::ui.feedback.alert>

<x-daisy::templates.auth.login-simple />
</code-snippet>
@endverbatim

### Host App Integration

For a standard host application, publish the package config and built assets:

@verbatim
<code-snippet name="Publish Daisy Kit config and assets" lang="bash">
php artisan vendor:publish --tag=daisy-config
php artisan vendor:publish --tag=daisy-assets
</code-snippet>
@endverbatim

This is the default integration path. It allows the host app to use the package's published build and manifest without recompiling package-internal frontend dependencies.

Only publish `daisy-assets-source` when the host application explicitly wants to own the Vite pipeline and compile the package source itself.

### Overrides

If the host application needs to customize package output, prefer publishing and overriding package resources instead of rebuilding equivalent UI from scratch:

- `daisy-views`
- `daisy-templates`
- `daisy-lang`

@verbatim
<code-snippet name="Publish Daisy Kit override targets" lang="bash">
php artisan vendor:publish --tag=daisy-views
php artisan vendor:publish --tag=daisy-templates
php artisan vendor:publish --tag=daisy-lang
</code-snippet>
@endverbatim

### Configuration

Package behavior is configured through `config('daisy-kit.*')`.

Common integration points:

- `daisy-kit.auto_assets`: automatically push package CSS and JS to Blade stacks
- `daisy-kit.use_vite`: prefer Vite / manifest-aware loading
- `daisy-kit.vite_build_directory`: published asset build directory
- `daisy-kit.csrf_refresh.*`: CSRF refresh endpoint behavior

@verbatim
<code-snippet name="Read Daisy Kit config" lang="php">
config('daisy-kit.auto_assets');
config('daisy-kit.use_vite');
config('daisy-kit.csrf_refresh.enabled');
</code-snippet>
@endverbatim

### Conventions

- Keep reusable UI concerns in the package and host-specific business logic in the consuming application.
- Preserve concise public Blade usage such as `x-daisy::ui.inputs.button` and `x-daisy::templates.auth.login-simple`.
- Avoid introducing redundant package component or template file names because the package test suite enforces view naming conventions.

### Security

Some components and templates intentionally accept trusted HTML fragments, rich slots, SVG, or `HtmlString` content.

Never pass unsanitized user input into those surfaces from the host application.
