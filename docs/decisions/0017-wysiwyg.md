# ADR-0017: Trix WYSIWYG in v6.1

## Decision

Add `x-daisy-kit::wysiwyg` as the thirteenth independent module for v6.1.0. It
depends directly on Trix `^2.1.19` and exposes the active `Trix.Editor` through
`getTrixEditor()` for advanced Trix features. No v4 alias or shared editor loader
is restored.

Trix and its bundled DOMPurify configuration sanitize initial values, API values,
pastes and editor rendering in the browser. This is a usability boundary, not a
server trust boundary. Hosts must sanitize submitted HTML on the server before
storing or rendering it with unescaped Blade output.

Attachments are rejected by default. When enabled, Daisy Kit emits the file to
host code, tracks progress and blocks native submission until the host resolves
the attachment with a permanent HTTP or HTTPS URL. Daisy Kit provides no upload
endpoint, storage integration or Livewire adapter.

The module removes Trix UI, listeners, file references and temporary resources on
unmount. Trix may generate nonce-authorized style elements and runtime style
attributes. Hosts therefore provide a `trix-csp-nonce` meta element, authorize the
nonce in `style-src`, and allow `style-src-attr 'unsafe-inline'`. File previews also
require `img-src blob:`.

References: [Trix README](https://github.com/basecamp/trix),
[Trix HTML sanitization](https://github.com/basecamp/trix#html-sanitization), and
[DaisyUI components](https://daisyui.com/components/).
