import { insertNewlineAndIndent } from '@codemirror/commands';
import { syntaxTree } from '@codemirror/language';

// Only repair a missing separator between complete JSON members. A newline
// before a closing bracket must never introduce an invalid trailing comma.
export function jsonNewline(view) {
    const { state } = view;
    const selection = state.selection.main;
    if (state.readOnly || state.selection.ranges.length !== 1 || !selection.empty) return false;
    const head = selection.head;
    let value = syntaxTree(state).resolveInner(head, -1);
    while (value.parent && value.to === head && !['String', 'Number', 'True', 'False', 'Null', 'Object', 'Array'].includes(value.name)) value = value.parent;
    if (value.to !== head || !['String', 'Number', 'True', 'False', 'Null', 'Object', 'Array'].includes(value.name)) return false;
    const member = value.parent?.name === 'Property' ? value.parent : value;
    const container = member.parent;
    if (!container || !['Object', 'Array'].includes(container.name)) return false;
    const following = state.sliceDoc(head, Math.min(container.to, head + 1000)).trimStart();
    if (!following || !(container.name === 'Object' ? /^"/ : /^["{\[\-\dtfn]/).test(following)) return false;
    try { JSON.parse(state.sliceDoc(value.from, value.to)); } catch { return false; }
    const comma = { from: head, insert: ',' };
    const intermediate = state.update({ changes: comma, selection: { anchor: head + 1 } }).state;
    return insertNewlineAndIndent({
        state: intermediate,
        dispatch(transaction) {
            view.dispatch(state.update({ changes: comma }, {
                changes: transaction.changes, selection: transaction.selection,
                sequential: true, scrollIntoView: true, userEvent: 'input',
            }));
        },
    });
}
