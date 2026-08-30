## Laravel Daisy Kit

Laravel Daisy Kit is a PHP 8.4 / Laravel 13 package of focused Blade modules for applications
that already own their Tailwind CSS and DaisyUI setup. It provides exactly these components:

- `x-daisy-kit::table`, `x-daisy-kit::tree`, `x-daisy-kit::blueprint`, `x-daisy-kit::file-preview`, and `x-daisy-kit::map`
- `x-daisy-kit::copyable`, `x-daisy-kit::combobox`, `x-daisy-kit::signature`, `x-daisy-kit::truncate`, `x-daisy-kit::scrollspy`, and `x-daisy-kit::transfer-list`

This is a Composer/VCS package, not an npm package. In the host Vite configuration, resolve the
stable `@daisy-kit` alias to `vendor/art35rennes/laravel-daisy-kit/dist`; then import each used
entry explicitly, such as `@daisy-kit/table.js` and `@daisy-kit/table.css`. Every module exposes
`mount(root)`, `mountAll(scope = document)`, `unmount(root)`, and `getInstance(root)`; do not add a global bootstrap
or make one module load another implicitly. `mount` and `getInstance` return the same stable facade;
getters return detached snapshots, commands report success, and operational failures emit
`daisy-kit:{module}:error` with a machine-readable `code` and safe `message`. Listen only to
`daisy-kit:{module}:*` events.

Configuration is escaped, non-executable JSON. Preserve the modular CSP boundary: no inline
script, handler, view-authored style attribute, or view-authored style block. File Preview keeps
untrusted document rendering in its sandboxed child frame; its auxiliary chunks are emitted by
the explicit Vite entry and need no route, proxy, copy, or published asset.

Signature and Transfer List use dependencies that write runtime DOM styles. Pages mounting either
module require `style-src-attr 'unsafe-inline'`; all other parent-page modules keep
`style-src-attr 'none'`.

There is no compatibility layer, alias namespace, asset publication, route, or host template.
The differentiated product outcomes (focused interaction, editable data/graph/geospatial workflows,
and isolated document/media previews) are defined by the package's
`docs/specs/v5-product-contract-matrix.md`; do not reduce them to a successful mount state.
For implementation and verification details, activate the `laravel-daisy-kit-development` skill.
Keep the package Workbench a representative Laravel host with normal Blade, Vite, routes, and
forms. Do not turn it into an API explorer, event console, or interactive documentation surface.
