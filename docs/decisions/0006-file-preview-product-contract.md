# ADR-006: Restore the File Preview product contract

## Status

Accepted — 2026-08-27

## Context

The initial v5 File Preview retained its isolated renderer but reduced the v4 product
surface from file metadata, capability detection, compact/card/modal presentations and
media-specific controls to six props and one generic card. The reduction also conflated
presentation with preview mode, exposed irrelevant controls, and left the preview frame
outside the visible modal box.

## Decision

`x-daisy-kit::file-preview` remains the only public Blade entry. Its corrective contract
uses `file` or `url` plus normalized metadata, separates `layout` (`card`, `compact-list`,
`action-only`) from `previewMode` (`auto`, `inline`, `modal`, `download`), and restores
validated previews for image, video, audio, PDF, text and DOCX. Unsupported document,
spreadsheet, presentation and archive types remain useful metadata/download cards.

Blade renders semantic DaisyUI markup and private internal views. Named slots may replace
the trigger, metadata, actions, notice and modal footer without creating another component
alias. The old v4 `file-preview-trigger` alias and the reduced v5 alpha `src`, `maxBytes`
and modal-as-layout dialects are not retained.

The ESM entry keeps `mount`, `mountAll` and `unmount`. `mount(root)` returns an instance-local
facade, also available through `getInstance(root)`, with state, open/close, retry and zoom
controls. It does not expose the child frame or create a browser global.

Untrusted content continues to render in an opaque-origin `srcdoc` iframe without
`allow-same-origin`. Parent and child authenticate messages with the exact window source,
an instance token and a render identifier. Source URLs, response MIME types and payload
sizes are validated before rendering; pending requests and object URLs are released on
retry and unmount.

Modal previews repeat the validated download action in their footer so the original remains
reachable while inspecting it. DOCX zoom transforms the complete rendered document, not only
the control state, and the isolated frame keeps its own stylesheet while `docx-preview` writes
generated document styles to a dedicated container. Multipage DOCX content scrolls vertically
inside the bounded frame; multipage PDF navigation remains owned by the browser PDF viewer.

## Consequences

- The package restores the v4 user outcomes without restoring v4 aliases, global assets,
  routes or public DaisyUI wrappers.
- Hosts can compose custom controls through slots and the stable facade while the renderer
  remains private and replaceable.
- The host CSP stays strict. Only the opaque child permits the renderer's required styles,
  data/blob media and Vite-emitted internal assets.
- Browser fixtures are genuine text, SVG, WAV, MP4, PDF and DOCX files; the PDF and DOCX fixtures
  contain three pages so scrolling and paging are outcome-tested rather than inferred.
- Existing v5 alpha File Preview markup is intentionally incompatible with the corrective
  prerelease and must migrate from `src` to `url` and from modal layouts to `previewMode`.

## Alternatives rejected

- Reusing the v4 DOM and global runtime: loses v5 isolation, lifecycle and CSP guarantees.
- Keeping the six-prop alpha contract: cannot express the documented product outcomes.
- Reintroducing `file-preview-trigger`: expands the seven-entry public surface for a use
  case already covered by `layout="action-only"`, slots and the facade.
