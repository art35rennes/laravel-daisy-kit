# ADR-0008: Stable integrator facades and a strict Workbench boundary

## Status

Accepted

## Date

2026-08-29

## Context

ADR-0007 reduced the v5 public surface to eleven independently mounted modules. Every entry already
shared lifecycle exports, but the value returned by `mount()` was not equally useful: Table and Map
had rich facades while Tree, Blueprint, and File Preview exposed lifecycle cleanup only. The focused
modules also differed in command returns and error payloads.

The Testbench Workbench is useful because it behaves like a real Laravel host: Blade renders the
components, Vite loads explicit package entries, local routes serve deterministic data, and browser
tests exercise normal forms and interactions. Turning that application into an API explorer would
mix package documentation with test-only UI and cease to represent a normal consuming application.

## Decision

Every public ESM entry has one predictable lifecycle and one stable, module-specific facade:

- `mount(root)` returns the facade, reusing it when the root is already mounted, or `null` when
  configuration or initialization fails;
- `mountAll(scope)` returns the facade-or-`null` result for each matching root in DOM order;
- `getInstance(root)` returns the exact mounted facade or `null`;
- `unmount(root)` destroys owned resources, restores owned DOM state, and returns whether an instance
  was removed.

Getters return detached snapshots. Synchronous commands return `boolean`; asynchronous commands
return `Promise<boolean>`. Expected operational failures do not throw: they return `false` and emit
`daisy-kit:{module}:error` with `{ code, message, ...context }`. Programmer errors at the module
boundary, such as passing a non-Element root, may still throw a `TypeError`. A documented no-op or
rejected command target returns `false` without an error event because no runtime operation failed.

Tree, Blueprint, and File Preview receive explicit integration facades. No facade exposes TanStack,
Dagre, SignaturePad, SortableJS, iframe channels, or other private runtimes. The already documented
`Map.getLeafletMap()` method remains the sole third-party escape hatch.

The Workbench remains an internal Laravel host application. It may contain representative Blade,
native forms, deterministic host routes, and normal user controls. It must not contain an API
console, event logger, component inspector, or visible control whose only purpose is exercising a
facade. Facade contracts are explained in Markdown, proven in Vitest, and may be driven invisibly by
browser tests.

## Alternatives considered

### Keep lifecycle-only instances for smaller modules

Rejected because an integrator would have to mutate private DOM or dispatch synthetic clicks for
ordinary application coordination.

### Expose third-party instances from every facade

Rejected because it would make implementation dependencies part of the public contract and prevent
internal replacement. Map retains its existing exception for integrator-owned Leaflet plugins.

### Make the Workbench an interactive API catalogue

Rejected because it would add non-representative UI and duplicate the normative documentation. A
Laravel package Workbench should demonstrate the package inside a conventional host application.

## Consequences

- The public contract can specify return values and event payloads consistently across all eleven
  modules.
- Consumers can coordinate a component without relying on private markup.
- Operational errors are machine-readable and remain accessible in the rendered component.
- The Workbench stays small and representative; documentation and contract tests carry API detail.
- Changes to a facade method, return type, event name, or `detail` shape require a contract update.
