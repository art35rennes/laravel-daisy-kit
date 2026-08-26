## Laravel Daisy Kit

Laravel Daisy Kit is a PHP 8.4 / Laravel 13 package of focused Blade modules for applications
that already own their Tailwind CSS and DaisyUI setup. It provides exactly these components:

- `x-daisy-kit::forms.viewer` and `x-daisy-kit::forms.builder`
- `x-daisy-kit::table`, `x-daisy-kit::tree`, and `x-daisy-kit::blueprint`
- `x-daisy-kit::file-preview` and `x-daisy-kit::map`

Import each used ESM and CSS file explicitly from `art35rennes/laravel-daisy-kit/dist`. Every
module exposes `mount(root)`, `mountAll(scope = document)`, and `unmount(root)`; do not add a
global bootstrap or make one module load another implicitly. Listen only to
`daisy-kit:{module}:*` events.

Configuration is escaped, non-executable JSON. Preserve the modular CSP boundary: no inline
script, handler, view-authored style attribute, or view-authored style block. File Preview keeps
untrusted document rendering in its sandboxed child frame.

There is no compatibility layer, alias namespace, asset publication, route, or host template.
For implementation and verification details, activate the `laravel-daisy-kit-development` skill.
