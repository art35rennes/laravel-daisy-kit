# ADR 0002 — Modular Content Security Policy

## Status

Accepted — 2026-08-26.

## Decision

The host application owns its HTTP CSP. Daisy Kit requires external, explicitly imported
ESM and CSS only: it emits no executable configuration, inline script, inline handler, global,
`eval`, view-authored `style` attribute, or view-authored `<style>` block. Component data stays
in escaped JSON script data blocks.

Host modules are verified in a real browser with `script-src 'self'`, `style-src 'self'`, and
`style-src-attr 'none'`. This last directive deliberately still permits direct CSSOM property
assignments, while blocking `setAttribute('style', ...)` and `style.cssText`; tests assess the
browser's violations rather than inspecting a final DOM snapshot.

`forms.viewer`, `forms.builder`, `table`, `tree`, and `blueprint` require no exception. `map`
is first verified under that same policy; a real violation requires replacing the map engine with
the documented MapLibre + Terra Draw integration and its narrow worker/image directives.

Untrusted document previews are rendered only in a separately loaded iframe with `sandbox`
without `allow-same-origin`; its child policy permits only its two same-origin external scripts,
data/blob media and the document renderer's inline styles. It denies connections, forms and
navigation. An opaque sandbox origin emits `message` events with `origin === 'null'`, so the
parent authenticates every message by both `event.source === iframe.contentWindow` and a random
per-instance token. Sending back to an opaque origin necessarily uses `targetOrigin: '*'`; no
weaker trust decision is made from that value. The parent validates source, type and size, aborts
requests on unmount, and removes the frame and message listener.

## Sources

- [MDN: `style-src-attr`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Security-Policy/style-src-attr)
- [OWASP: Content Security Policy Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Content_Security_Policy_Cheat_Sheet.html)
- [OWASP: HTML5 Security Cheat Sheet — sandboxed frames](https://cheatsheetseries.owasp.org/cheatsheets/HTML5_Security_Cheat_Sheet.html)
- [web.dev: Strict CSP](https://web.dev/articles/strict-csp)
- [MapLibre GL JS: CSP directives](https://maplibre.org/maplibre-gl-js/docs/guides/v5-to-v6-migration-guide/)
- [docx-preview: `renderAsync` and `styleContainer`](https://github.com/VolodymyrBaydalka/docxjs#api)
