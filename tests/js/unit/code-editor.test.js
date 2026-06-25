/** @vitest-environment jsdom */

import { describe, expect, it } from 'vitest';

import { readJsonPayload } from '../../../resources/js/code-editor.js';

describe('code-editor payload reader', () => {
  it('reads json payloads rendered inside template content', () => {
    document.body.innerHTML = `
      <div>
        <template data-initial>{"value":"{\\n  \\"hello\\": \\"world\\"\\n}"}</template>
      </div>
    `;

    const payload = readJsonPayload(document.body, 'template[data-initial]', {});

    expect(payload).toEqual({
      value: "{\n  \"hello\": \"world\"\n}",
    });
  });

  it('still reads json payloads rendered inside script tags', () => {
    document.body.innerHTML = `
      <div>
        <script type="application/json" data-initial>{"value":"ok"}</script>
      </div>
    `;

    expect(readJsonPayload(document.body, 'script[data-initial]', {})).toEqual({
      value: 'ok',
    });
  });
});
