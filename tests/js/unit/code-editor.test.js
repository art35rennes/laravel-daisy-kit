/** @vitest-environment jsdom */

import { describe, expect, it } from 'vitest';

import { expand, readJsonPayload, reduce } from '../../../resources/js/code-editor.js';

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

  it('moves an editor into a full-screen dialog and restores it when closed', () => {
    document.body.innerHTML = `
      <section>
        <div
          id="editor"
          class="code-editor"
          data-expand-modal-id="editor-expand-modal"
          data-expand-label="Expand"
          data-expand-title="Expand editor"
          data-reduce-label="Reduce"
          data-reduce-title="Reduce editor"
        >
          <button type="button" data-action="expand" data-code-editor-expand-button>Expand</button>
        </div>
        <dialog id="editor-expand-modal">
          <div data-code-editor-expand-host></div>
          <div class="modal-backdrop">
            <button type="button" data-code-editor-expand-dismiss>Reduce</button>
          </div>
        </dialog>
      </section>
    `;

    const editor = document.getElementById('editor');
    const modal = document.getElementById('editor-expand-modal');
    modal.showModal = () => {
      modal.open = true;
    };
    modal.close = () => {
      modal.open = false;
      modal.dispatchEvent(new Event('close'));
    };

    expand(editor);

    expect(editor.classList.contains('is-expanded')).toBe(true);
    expect(modal.open).toBe(true);
    expect(modal.querySelector('[data-code-editor-expand-host]').contains(editor)).toBe(true);
    expect(editor.querySelector('[data-code-editor-expand-button]').dataset.action).toBe('reduce');
    expect(editor.querySelector('[data-code-editor-expand-button]').textContent).toBe('Reduce');

    reduce(editor);

    expect(editor.classList.contains('is-expanded')).toBe(false);
    expect(document.querySelector('section > .code-editor')).toBe(editor);
    expect(editor.querySelector('[data-code-editor-expand-button]').dataset.action).toBe('expand');
    expect(editor.querySelector('[data-code-editor-expand-button]').textContent).toBe('Expand');
  });

  it('closes the expanded editor from its backdrop without a nested dialog form', () => {
    document.body.innerHTML = `
      <form>
        <div
          id="editor"
          class="code-editor"
          data-expand-modal-id="editor-expand-modal"
          data-expand-label="Expand"
          data-expand-title="Expand editor"
          data-reduce-label="Reduce"
          data-reduce-title="Reduce editor"
        >
          <button type="button" data-action="expand" data-code-editor-expand-button>Expand</button>
        </div>
        <dialog id="editor-expand-modal">
          <div data-code-editor-expand-host></div>
          <div class="modal-backdrop">
            <button type="button" data-code-editor-expand-dismiss>Reduce</button>
          </div>
        </dialog>
      </form>
    `;

    const editor = document.getElementById('editor');
    const modal = document.getElementById('editor-expand-modal');
    modal.showModal = () => {
      modal.open = true;
    };
    modal.close = () => {
      modal.open = false;
      modal.dispatchEvent(new Event('close'));
    };

    expand(editor);
    modal.querySelector('[data-code-editor-expand-dismiss]').click();

    expect(modal.open).toBe(false);
    expect(editor.classList.contains('is-expanded')).toBe(false);
    expect(document.querySelector('form > .code-editor')).toBe(editor);
  });
});
