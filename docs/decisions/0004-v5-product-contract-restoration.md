# ADR-004: Restore the differentiating v5 product contract

## Status

Accepted — 2026-08-26

## Context

The v5.0.0 browser audit found that the initial rewrite kept the public names of six
differentiating modules while reducing several of their business contracts to demos.
The reduction was not an intentional product decision: the v4 source capabilities were
not represented by a parity oracle, and readiness-only tests could therefore validate an
unusable result.

## Decision

`docs/specs/v5-product-contract-matrix.md` is the single normative oracle for corrective
v5 work. It records the supported business outcomes for each v5 module, the retained v4
evidence, explicit non-goals, and the tests that prove the outcome. Product work starts
with a failing contract test and lands as a small vertical slice.

V5 remains a new package. It retains only the seven Blade entries under `x-daisy-kit::`
(the two Forms entries plus Table, Tree, Blueprint, File Preview and Map), explicit host
Vite imports, host-owned Tailwind/DaisyUI, module-local ESM/CSS entries, and no v4 alias
or adapter. DaisyUI classes may compose package semantic HTML; generic DaisyUI components
remain the host's responsibility.

Runtime failures must be observable through a structured `daisy-kit:{module}:error`
event. A missing Web Crypto `randomUUID()` is not a valid reason for a module to fail on
an HTTP development origin. Authentication material for the sandboxed File Preview frame
continues to require source-and-token validation; a non-Web-Crypto fallback is deliberately
scoped and tested rather than silently relying on a secure context.

## Consequences

- The prior intentionally reduced descriptions in the public-contract document are
  superseded by the matrix rather than treated as a release-quality ceiling.
- Every restored surface carries user-result tests, including a canonical v4-shaped
  fixture where relevant, plus browser verification for keyboard, multiple roots,
  destruction and CSP.
- This is corrective prerelease work only. Existing v5.0.0 and historical alpha tags are
  immutable; a stable replacement requires a green integration in the rebuilt demo.

## Alternatives rejected

- Reintroducing all legacy components or `x-daisy` aliases: violates the v5 boundary.
- Treating module `ready` state as product acceptance: cannot prove a business outcome.
- Deferring parity tests to the demo: leaves the package without its own contract oracle.
