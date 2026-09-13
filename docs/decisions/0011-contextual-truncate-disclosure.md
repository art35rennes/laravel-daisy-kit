# Contextual Truncate disclosure

Date: 2026-09-02

Truncate primarily serves dense values such as addresses in table cells. Its disclosure control
must therefore remain inline and contextual instead of opening an editorial-size surface detached
from its source.

When measured overflow exists, render a compact ellipsis button next to the clipped value. Hover
and keyboard focus open a temporary preview after a configurable delay. Click, Enter, Space, or
the public `open()` method pins that same preview so its complete plain text can be selected. A
pinned preview uses native light dismiss (outside interaction and Escape); an optional backdrop is
visible only in this pinned state. Pointer movement between the ellipsis and preview must not close
the temporary preview.

Use the invoker relationship of `popover="auto"` and CSS anchor positioning to place the preview
beside its ellipsis. The CSS fallback is viewport-centered, never an unpositioned top-left box. No
inline style writes or positioning dependency are introduced, preserving strict CSP compatibility.
The existing `refresh`, `isTruncated`, `open`, and `close` facade and `opened { text }` /
`closed { text }` events remain stable. The additive Blade props are `hover=true`,
`hoverDelay=250`, and `backdrop=false`.
