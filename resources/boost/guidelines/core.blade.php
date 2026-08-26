## Laravel Daisy Kit

Laravel Daisy Kit is a PHP 8.4 / Laravel 13 package of focused Blade modules for applications
that already own their Tailwind CSS and DaisyUI setup. It provides exactly these components:

- `x-daisy-kit::forms.viewer` and `x-daisy-kit::forms.builder`
- `x-daisy-kit::table`, `x-daisy-kit::tree`, and `x-daisy-kit::blueprint`
- `x-daisy-kit::file-preview` and `x-daisy-kit::map`

This is a Composer/VCS package, not an npm package. In the host Vite configuration, resolve the
stable `@daisy-kit` alias to `vendor/art35rennes/laravel-daisy-kit/dist`; then import each used
entry explicitly, such as `@daisy-kit/table.js` and `@daisy-kit/table.css`. Every module exposes
`mount(root)`, `mountAll(scope = document)`, and `unmount(root)`; do not add a global bootstrap
or make one module load another implicitly. Listen only to `daisy-kit:{module}:*` events.

Configuration is escaped, non-executable JSON. Preserve the modular CSP boundary: no inline
script, handler, view-authored style attribute, or view-authored style block. File Preview keeps
untrusted document rendering in its sandboxed child frame; its auxiliary chunks are emitted by
the explicit Vite entry and need no route, proxy, copy, or published asset.

There is no compatibility layer, alias namespace, asset publication, route, or host template.
The differentiated product outcomes (recursive Forms, editable data/graph/geospatial workflows,
and isolated document/media previews) are defined by the package's
`docs/specs/v5-product-contract-matrix.md`; do not reduce them to a successful mount state.
For implementation and verification details, activate the `laravel-daisy-kit-development` skill.
