import { describe, expect, it } from 'vitest';
import { EditorState } from '@codemirror/state';
import { history, undo } from '@codemirror/commands';
import { json } from '@codemirror/lang-json';
import { jsonNewline } from '../../../resources/js/code-editor/json-newline.js';

function editor(document) {
    const head = document.indexOf('|');
    const target = {
        state: EditorState.create({ doc: document.replace('|', ''), selection: { anchor: head }, extensions: [json(), history()] }),
        dispatch(transaction) { target.state = transaction.state; },
    };
    return target;
}

describe('JSON newline assistance', () => {
    it.each(['{\n  "first": 1|\n  "second": 2\n}', '[\n  "first"|\n  "second"\n]', '[\n  {"nested": true}|\n  null\n]'])('adds a missing separator in one undoable edit: %s', document => {
        const target = editor(document);
        const before = target.state.doc.toString();
        expect(jsonNewline(target)).toBe(true);
        expect(() => JSON.parse(target.state.doc.toString())).not.toThrow();
        expect(undo(target)).toBe(true);
        expect(target.state.doc.toString()).toBe(before);
    });

    it.each(['[1|]', '{"first":1|}', '[1|,2]', '["in|side",2]', '{"key"|:1}', '[tru| false]', '1|', '[|]'])('leaves ordinary newlines and incomplete values to CodeMirror: %s', document => {
        const target = editor(document);
        const before = target.state.doc.toString();
        expect(jsonNewline(target)).toBe(false);
        expect(target.state.doc.toString()).toBe(before);
    });
});
