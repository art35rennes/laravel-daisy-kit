import { Annotation, Compartment, EditorState } from '@codemirror/state';
import { EditorView, drawSelection, highlightActiveLine, highlightActiveLineGutter, keymap, lineNumbers } from '@codemirror/view';
import { defaultKeymap, history, historyKeymap, undo, redo, undoDepth, redoDepth } from '@codemirror/commands';
import { bracketMatching, foldGutter, foldKeymap, indentOnInput, syntaxHighlighting } from '@codemirror/language';
import { classHighlighter } from '@lezer/highlight';
import { search, searchKeymap, openSearchPanel, closeSearchPanel, searchPanelOpen } from '@codemirror/search';
import { autocompletion, closeBrackets, closeBracketsKeymap, completionKeymap, completionStatus, closeCompletion } from '@codemirror/autocomplete';
import { createMountable } from './core/mountable.js';
import { createInstanceIdentifier } from './core/identifiers.js';
import { loadLanguage } from './code-editor/languages.js';
import '../css/code-editor.css';

const origin = Annotation.define();

function initialize(root, configuration) {
    const input = root.querySelector('textarea');
    const host = root.querySelector('[data-code-editor-host]');
    if (!(input instanceof HTMLTextAreaElement) || !(host instanceof HTMLElement)) {
        throw new Error('Code Editor requires a textarea and editor host.');
    }
    const label = typeof configuration.label === 'string' ? configuration.label : 'Code';
    const labels = { line: 'Ln', column: 'Col', readOnly: 'Read only', copied: 'Copied', required: 'Please enter code.', ...(configuration.labels ?? {}) };
    const initialValue = input.value;
    const original = { tabIndex: input.getAttribute('tabindex'), ariaHidden: input.getAttribute('aria-hidden'), readOnly: input.readOnly, id: input.id };
    const languageSlot = new Compartment();
    const readOnlySlot = new Compartment();
    const wrappingSlot = new Compartment();
    const historySlot = new Compartment();
    let active = true;
    let language = 'text';
    let languageRevision = 0;
    let readOnly = configuration.readOnly === true;
    let lineWrapping = configuration.lineWrapping === true;
    let expanded = false;
    let previousFocus = null;
    let previousScroll = null;
    let feedbackTimer = null;
    let lastError = null;
    let view;
    const toolbar = root.querySelector('[data-code-editor-toolbar]');
    const position = root.querySelector('[data-code-editor-position]');
    const feedback = root.querySelector('[data-code-editor-feedback]');
    const languageLabel = root.querySelector('[data-code-editor-language]');
    const controller = new AbortController();
    const disabled = () => input.matches(':disabled') || configuration.disabled === true;
    const emit = (name, detail = {}) => root.dispatchEvent(new CustomEvent(`daisy-kit:code-editor:${name}`, { bubbles: true, detail }));
    function error(code, message) {
        lastError = code;
        const status = root.querySelector('[data-daisy-kit-status]');
        if (status) { status.textContent = message; status.hidden = false; }
        emit('error', { code, message });
        return false;
    }
    function clearError(code) {
        if (lastError !== code) return;
        const status = root.querySelector('[data-daisy-kit-status]');
        if (status) { status.textContent = ''; status.hidden = true; }
        lastError = null;
    }
    function editableExtensions() {
        return [EditorState.readOnly.of(readOnly || disabled()), EditorView.editable.of(!readOnly && !disabled()),
            EditorView.contentAttributes.of({ 'aria-label': label, 'aria-readonly': String(readOnly), 'aria-required': String(input.required), 'aria-disabled': String(disabled()), tabindex: disabled() ? '-1' : '0', ...(root.hasAttribute('aria-describedby') ? { 'aria-describedby': root.getAttribute('aria-describedby') } : {}) })];
    }
    function updateControls() {
        if (!view) return;
        const head = view.state.selection.main.head;
        const line = view.state.doc.lineAt(head);
        if (position) position.textContent = `${labels.line} ${line.number}, ${labels.column} ${head - line.from + 1}${readOnly ? ` · ${labels.readOnly}` : ''}`;
        root.querySelectorAll('[data-code-editor-action]').forEach(button => {
            const action = button.dataset.codeEditorAction;
            button.hidden = ['undo', 'redo'].includes(action) && readOnly;
            button.disabled = disabled() || (action === 'undo' && undoDepth(view.state) === 0) || (action === 'redo' && redoDepth(view.state) === 0);
            if (action === 'wrap') button.setAttribute('aria-pressed', String(lineWrapping));
            if (action === 'expand') button.setAttribute('aria-pressed', String(expanded));
        });
    }
    view = new EditorView({
        parent: host,
        state: EditorState.create({ doc: input.value, extensions: [
            languageSlot.of([]), readOnlySlot.of(editableExtensions()), wrappingSlot.of(lineWrapping ? EditorView.lineWrapping : []), historySlot.of(history()),
            EditorState.tabSize.of(Number.isInteger(configuration.tabSize) && configuration.tabSize > 0 && configuration.tabSize <= 16 ? configuration.tabSize : 4),
            configuration.lineNumbers === false ? [] : [lineNumbers(), highlightActiveLineGutter()],
            drawSelection(), highlightActiveLine(), indentOnInput(), bracketMatching(), foldGutter(),
            syntaxHighlighting(classHighlighter), autocompletion(), closeBrackets(), search({ top: true }),
            EditorView.cspNonce.of(typeof configuration.nonce === 'string' ? configuration.nonce : ''),
            EditorState.phrases.of(configuration.phrases ?? {}),
            keymap.of([...closeBracketsKeymap, ...defaultKeymap, ...searchKeymap, ...historyKeymap, ...foldKeymap, ...completionKeymap]),
            EditorView.updateListener.of(update => {
                if (update.docChanged) {
                    input.value = update.state.doc.toString();
                    view?.contentDOM.removeAttribute('aria-invalid');
                    clearError('required');
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    emit('change', { value: input.value, origin: update.transactions.find(transaction => transaction.annotation(origin))?.annotation(origin) ?? 'user' });
                }
                updateControls();
            }),
        ] }),
    });
    input.id ||= createInstanceIdentifier('code-editor-value');
    input.tabIndex = -1;
    input.setAttribute('aria-hidden', 'true');
    input.classList.add('daisy-kit-code-editor__value');
    input.readOnly = readOnly;
    root.dataset.codeEditorMounted = '';
    if (toolbar) toolbar.hidden = configuration.toolbar === false;

    function setValue(value, source = 'api') {
        if (!active || typeof value !== 'string') return false;
        if (value === view.state.doc.toString()) return true;
        view.dispatch({ changes: { from: 0, to: view.state.doc.length, insert: value }, selection: { anchor: 0 }, effects: historySlot.reconfigure([]), annotations: origin.of(source) });
        view.dispatch({ effects: historySlot.reconfigure(history()) });
        return true;
    }
    async function setLanguage(value) {
        if (!active || typeof value !== 'string') return false;
        const revision = ++languageRevision;
        try {
            const extension = await loadLanguage(value);
            if (!active || revision !== languageRevision) return false;
            view.dispatch({ effects: languageSlot.reconfigure(extension) });
            language = value;
            clearError('language-unavailable');
            if (languageLabel) languageLabel.textContent = value;
            emit('language-changed', { language });
            return true;
        } catch {
            if (!active || revision !== languageRevision) return false;
            return error('language-unavailable', 'The requested language could not be loaded.');
        }
    }
    function setReadOnly(value) {
        if (!active || typeof value !== 'boolean') return false;
        readOnly = value;
        input.readOnly = value;
        closeCompletion(view);
        closeSearchPanel(view);
        view.dispatch({ effects: readOnlySlot.reconfigure(editableExtensions()) });
        updateControls();
        return true;
    }
    function setLineWrapping(value) {
        if (!active || typeof value !== 'boolean') return false;
        lineWrapping = value;
        view.dispatch({ effects: wrappingSlot.reconfigure(value ? EditorView.lineWrapping : []) });
        return true;
    }
    function focus() {
        if (!active || disabled()) return false;
        view.focus();
        return true;
    }
    function setExpanded(value) {
        if (!active || typeof value !== 'boolean' || (value && disabled())) return false;
        if (expanded === value) return true;
        if (value) {
            previousFocus = document.activeElement;
            previousScroll = { top: view.scrollDOM.scrollTop, left: view.scrollDOM.scrollLeft, x: window.scrollX, y: window.scrollY };
        }
        expanded = value;
        root.classList.toggle('daisy-kit-code-editor--expanded', value);
        view.requestMeasure();
        if (value) focus();
        else if (previousScroll) {
            view.scrollDOM.scrollTop = previousScroll.top;
            view.scrollDOM.scrollLeft = previousScroll.left;
            previousFocus?.focus({ preventScroll: true });
            window.scrollTo(previousScroll.x, previousScroll.y);
        }
        updateControls();
        emit('expanded', { expanded });
        return true;
    }
    async function copy() {
        if (!active || disabled()) return false;
        try {
            await navigator.clipboard.writeText(input.value);
            if (!active) return false;
            clearError('clipboard-unavailable');
            if (feedback) {
                feedback.textContent = labels.copied;
                clearTimeout(feedbackTimer);
                feedbackTimer = setTimeout(() => { feedback.textContent = ''; }, 2000);
            }
            emit('copied');
            return true;
        } catch { return active ? error('clipboard-unavailable', 'Code could not be copied. Select it and copy manually.') : false; }
    }
    const command = operation => active && !disabled() && !readOnly ? operation(view) : false;
    const openSearch = () => active && !disabled() ? openSearchPanel(view) : false;
    const actions = { copy, search: openSearch, undo: () => command(undo), redo: () => command(redo), wrap: () => setLineWrapping(!lineWrapping), expand: () => setExpanded(!expanded) };
    root.addEventListener('click', event => {
        const button = event.target.closest('[data-code-editor-action]');
        if (button && root.contains(button) && !button.disabled) actions[button.dataset.codeEditorAction]?.();
    }, { signal: controller.signal });
    root.addEventListener('keydown', event => {
        if (event.key !== 'Escape') return;
        if (event.defaultPrevented || completionStatus(view.state) !== null) return;
        if (searchPanelOpen(view.state)) { closeSearchPanel(view); event.preventDefault(); return; }
        if (expanded) { setExpanded(false); event.preventDefault(); }
    }, { signal: controller.signal });
    input.addEventListener('invalid', event => {
        event.preventDefault();
        view.contentDOM.setAttribute('aria-invalid', 'true');
        error('required', labels.required);
        focus();
    }, { signal: controller.signal });
    root.addEventListener('focusout', event => {
        if (!root.contains(event.relatedTarget)) input.dispatchEvent(new Event('change', { bubbles: true }));
    }, { signal: controller.signal });
    input.form?.addEventListener('reset', event => {
        queueMicrotask(() => { if (active && !event.defaultPrevented) setValue(initialValue, 'reset'); });
    }, { signal: controller.signal });
    const observer = new MutationObserver(() => {
        if (active) { view.dispatch({ effects: readOnlySlot.reconfigure(editableExtensions()) }); updateControls(); }
    });
    for (let ancestor = input.parentElement; ancestor; ancestor = ancestor.parentElement) {
        if (ancestor.tagName === 'FIELDSET') observer.observe(ancestor, { attributes: true, attributeFilter: ['disabled'] });
    }
    observer.observe(input, { attributes: true, attributeFilter: ['disabled', 'required'] });
    updateControls();
    void setLanguage(configuration.language ?? 'text');
    return {
        getValue: () => input.value,
        getState: () => ({ language, readOnly, disabled: disabled(), lineWrapping, expanded, line: view.state.doc.lineAt(view.state.selection.main.head).number, column: view.state.selection.main.head - view.state.doc.lineAt(view.state.selection.main.head).from + 1 }),
        setValue: value => setValue(value), setLanguage, setReadOnly, setLineWrapping, focus,
        undo: actions.undo, redo: actions.redo, openSearch, copy, setExpanded,
        destroy() {
            if (expanded) setExpanded(false);
            active = false;
            languageRevision += 1;
            controller.abort();
            observer.disconnect();
            clearTimeout(feedbackTimer);
            view.destroy();
            input.classList.remove('daisy-kit-code-editor__value');
            input.readOnly = original.readOnly;
            input.id = original.id;
            for (const [attribute, value] of [['tabindex', original.tabIndex], ['aria-hidden', original.ariaHidden]]) {
                if (value === null) input.removeAttribute(attribute); else input.setAttribute(attribute, value);
            }
            if (toolbar) toolbar.hidden = true;
            delete root.dataset.codeEditorMounted;
        },
    };
}

export const { mount, mountAll, unmount, getInstance } = createMountable('code-editor', initialize);
