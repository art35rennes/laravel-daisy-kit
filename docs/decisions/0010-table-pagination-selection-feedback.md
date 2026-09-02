# Table pagination and deferred selection feedback

Date: 2026-08-30

The page-size control must always display the effective page size, including an
initial, restored or API-supplied size absent from `pageSizeOptions`. Add that size
to the control without changing the other choices or the pagination contract.

The optional selection configuration `summaryVisibility: 'after-first-selection'`
hides the initial zero-selection summary, not the multiple-selection controls.
Reveal it when a non-empty selection is first observed, including initial or
API-driven selections; keep it visible after clearing. This interaction state is
per mounted instance and resets on remount. The default `'always'` preserves
existing behavior (including hiding an empty single-selection bar).

No alias, global or host-specific table behavior is introduced.
