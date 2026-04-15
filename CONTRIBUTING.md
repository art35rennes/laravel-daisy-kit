# Contributing

## Versioning policy

This package follows [Semantic Versioning 2.0.0](https://semver.org/lang/fr/).

Release numbers use the `MAJOR.MINOR.PATCH` format:

- `MAJOR`: incompatible change on the public API of the package
- `MINOR`: backward-compatible feature or extension
- `PATCH`: backward-compatible bug fix, documentation-only fix, or internal maintenance with no public API break

The public API of this package includes:

- the Composer package name `art35rennes/laravel-daisy-kit`
- the PHP namespace `Art35rennes\DaisyKit`
- the published configuration contract
- published asset paths and manifest contract used by host apps
- Blade component names under the `daisy::` namespace
- template names under `daisy::templates.*`
- translation namespace `daisy::`

## AI surface maintenance

This package also ships AI guidance intended to make host applications aware of the package component and template surface:

- `resources/boost/guidelines/core.blade.php`
- `resources/boost/skills/daisy-kit-component-reuse/`
- generated references under `resources/boost/skills/daisy-kit-component-reuse/references/`

If you add, rename, or remove a public component, layout, or template, regenerate the AI catalog and commit the updated references:

```bash
composer ai:catalog
```

## Release rules

- Any breaking change must be documented before release.
- Any release must update `CHANGELOG.md`.
- Tags must follow the `vMAJOR.MINOR.PATCH` format.
- Until a contrary decision is documented, the stable public baseline starts at `v1.0.0`.

## Initial release baseline

The initial stable release of this package is `v1.0.0`.
It establishes the current package surface as the compatibility baseline for future releases.
