## Laravel Daisy Kit

Laravel Daisy Kit is a PHP 8.4 / Laravel 13 package of focused Blade modules for applications
that already own their Tailwind CSS and DaisyUI setup. It provides exactly these components:

- `x-daisy-kit::table`, `x-daisy-kit::tree`, `x-daisy-kit::blueprint`, `x-daisy-kit::file-preview`, and `x-daisy-kit::map`
- `x-daisy-kit::copyable`, `x-daisy-kit::combobox`, `x-daisy-kit::signature`, `x-daisy-kit::truncate`, `x-daisy-kit::scrollspy`, `x-daisy-kit::transfer-list`, `x-daisy-kit::code-editor`, and `x-daisy-kit::wysiwyg`

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

Signature, Transfer List and WYSIWYG use dependencies that write runtime DOM styles. Pages mounting
one of these modules require `style-src-attr 'unsafe-inline'`. WYSIWYG also needs the Trix nonce in
`style-src` and `img-src blob:` for local attachment previews. All other parent-page modules keep
`style-src-attr 'none'`. Submitted WYSIWYG HTML must be sanitized by the host on the server before
unescaped rendering.

There is no compatibility layer, alias namespace, asset publication, route, or host template.
The differentiated product outcomes (focused interaction, editable data/graph/geospatial workflows,
and isolated document/media previews) are defined by the package's
`docs/specs/v6-product-contract-matrix.md`; do not reduce them to a successful mount state.
For implementation and verification details, activate the `laravel-daisy-kit-development` skill.
Keep the package Workbench a representative Laravel host with normal Blade, Vite, routes, and
forms. Do not turn it into an API explorer, event console, or interactive documentation surface.
