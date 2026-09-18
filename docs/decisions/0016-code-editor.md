# Code Editor in v6

Add `x-daisy-kit::code-editor` as the twelfth independent module. The accepted
implementation plan supersedes the former CodeMirror exclusion. Use CodeMirror 6
directly, with a synchronous mounted facade and asynchronously loaded language
grammars. Keep the engine private and use DaisyUI variables for all presentation.

Native textarea submission, validation, reset and a no-JavaScript fallback belong
to this component. Persistence, Blade parsing, formatting, execution, collaboration
and multi-document workspaces do not. Supply the host CSP nonce to CodeMirror's
generated styles; verify runtime style attributes under the existing strict policy.

References: https://codemirror.net/examples/config/,
https://codemirror.net/examples/readonly/, https://mantine.dev/x/code-highlight/.
