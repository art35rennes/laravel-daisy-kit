# Laravel Daisy Kit

Laravel Daisy Kit is a reusable Laravel package that provides Blade UI components, templates, translations, and frontend assets built around DaisyUI and Tailwind CSS.

## Package Scope

This repository now contains only package concerns:

- `src/`
- `config/daisy-kit.php`
- `resources/views`
- `resources/lang`
- `resources/js`
- `resources/css`
- package-focused tests in `tests/`

The package keeps:

- PHP namespace `Art35rennes\DaisyKit`
- Blade namespace `daisy::`

It no longer contains:

- docs pages
- demo routes
- application controllers
- inventory tooling
- browser tests

Those concerns now live in a separate Laravel application repository named `laravel-daisy-kit-demo`.

## Local Package Development

```bash
composer install
npm install
composer test
npm run build
```

## Host App Integration

For a standard host application, prefer the published build assets:

```bash
php artisan vendor:publish --tag=daisy-config
php artisan vendor:publish --tag=daisy-assets
```

This gives the host app a public manifest under `public/vendor/art35rennes/laravel-daisy-kit`, which the package can consume directly without requiring the host to install package-internal Node dependencies.

If a host application explicitly wants to recompile package sources, it may publish them with:

```bash
php artisan vendor:publish --tag=daisy-assets-source
```

In that mode, the host is responsible for installing the corresponding frontend dependencies and wiring its own Vite pipeline.

## Security Notes

- The package ships only reusable library concerns.
- The CSRF refresh endpoint is configurable through `daisy-kit.csrf_refresh` and can be disabled.
- Some advanced components and templates intentionally accept trusted HTML fragments for rich rendering. Do not pass raw user content to those props without sanitizing it in the host application.

## Local Integration With The Demo App

Clone the two repositories side by side:

- `laravel-daisy-kit`
- `laravel-daisy-kit-demo`

The demo app consumes the package through a Composer `path` repository pointed at `../laravel-daisy-kit`.

This keeps the split explicit:

- the package remains installable in a normal Laravel application
- the demo app validates the real integration surface
- the two repositories can version independently

## Testing

The root `tests/` directory covers only package responsibilities:

- Blade component rendering
- reusable template rendering
- package helper behavior
- package endpoints such as `daisy-kit.csrf-token`

Integration, navigation, browser, and docs rendering tests belong to the separate demo application repository.
