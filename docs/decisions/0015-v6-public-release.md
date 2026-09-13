# ADR-015: Publish the focused contract as v6

## Status

Accepted — 2026-09-13.

## Decision

Release the eleven-module contract as **v6.0.0**, a new stable major. The public
v5.0.0 tag included Forms Viewer/Builder; their removal is incompatible and must
not ship as v5.1.0. Preserve every historical tag and decision without rewriting
them. There is no compatibility layer or automatic migration.

The current contract and outcome oracle become `docs/specs/v6-public-contract.md`
and `docs/specs/v6-product-contract-matrix.md`. Keep the `x-daisy-kit::` namespace,
independent ESM/CSS entries and `mount`, `mountAll`, `unmount`, `getInstance`
interfaces. PHP 8.4+ and Laravel 13 remain required; hosts own Tailwind, DaisyUI,
forms and any Livewire integration.

Distribute through the public GitHub VCS repository, with tracked reproducible
`dist/` output. Do not publish to Packagist or npm. Consumers declare the VCS
repository and require `^6.0`; the executable demo locks the exact `v6.0.0` tag.
The demo repository is delivered, without a hosted deployment.

Use `dev` for integration and `main` for stable releases. Validate PRs, pushes to
both branches and `v6.*` tags. Retire `next/v5` from active workflows while
preserving history. Full uncached tests, browser outcomes, fresh VCS installation,
audits and distribution reproducibility gate publication; TIA is supplementary.

Publish a stable GitHub release only after the immutable remote tag is verified
in a fresh consumer and the demo. Never move a published tag; subsequent fixes
receive a new patch version. A draft GitHub release is not publication evidence.
