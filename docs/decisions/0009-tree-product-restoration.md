# ADR-0009: Restore the hierarchical selector product contract

## Status

Accepted — 2026-08-30

## Decision

Keep `x-daisy-kit::tree` and its independent ESM/CSS entries. Extend the existing stable facade;
do not introduce aliases, a graph engine, reordering, editing, virtualization or inline styles.
Use the existing Match Sorter utility only for optional fuzzy matching, preserving sibling order.

Restore initial values, disabled subtrees, leaf or selected-root values, visible checkboxes,
separate disclosure controls, loaded-scope bulk actions and visible/hidden selection counts.
Multiple selection cascades over enabled descendants. Leaf mode never submits unloaded branches;
root mode can represent a selected lazy subtree and inherits that intention as children arrive.
The exact configured form name carries one ordered JSON array in both modes.

Search is automatic or explicit, local or remote. Remote `{ items }` responses carry ancestor paths
and merge into the canonical index without losing off-result selections. An empty search restores
the pre-search expansion. Branch failures are local and retryable; empty successful responses are
cached. Superseded requests and responses arriving after unmount cannot modify state.

PHP validates configuration and renders optional host Blade node views into inert templates.
Remote JSON never becomes HTML. Package controls retain ownership of selection and keyboard
navigation. All user-facing messages are translated and can be overridden per instance.

## Public additions

Props: `value`, `valueMode=leaves`, `disabled=false`, `initialExpandPaths=[]`,
`searchMode=auto`, `searchMatch=includes`, `searchDebounce=200`, `searchMin=0`,
`searchParam=query`, `nodeView=null`, `labels=[]`. Existing props retain their meaning.

Facade additions: detached `getState()`, `setSearch(query)`, asynchronous `applySearch()`,
`clearSearch()`, asynchronous `expandPath(ids)`, `expandAll()`, `collapseAll()`,
`selectVisible()`, asynchronous `reloadBranch(id)`. `expandAll` never fetches unknown branches.
Existing getters, commands and `change { value, values }` remain supported. Commands return boolean
or Promise<boolean>; operational failure emits `error { code, message, id?, query? }`.
New events: `search { query }` and `loading { loading, id?, query? }`.

Persistence stores expansion, selected values and known paths under an instance-specific key.
Explicit initial `value` overrides stored selection; stored expansion augments configured paths.
Disabled nodes remain visible and cannot be changed through either UI or facade commands.

## Proof

Outcome tests cover both value modes, indeterminate and disabled states, remote races and retries,
hidden selection, search reset, persisted lazy paths, form submission and independent instances.
Four ordinary Workbench scenarios exercise classification, permissions, a remote catalogue and
custom node presentation. Browser checks cover keyboard, responsive themes and strict CSP.
