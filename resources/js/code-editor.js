import { Annotation, Compartment, EditorState } from '@codemirror/state';
import { EditorView, drawSelection, highlightActiveLine, highlightActiveLineGutter, keymap, lineNumbers } from '@codemirror/view';
import { defaultKeymap, history, historyKeymap, isolateHistory, undo, redo, undoDepth, redoDepth } from '@codemirror/commands';
import { bracketMatching, foldAll, unfoldAll, foldable, foldedRanges, foldEffect, unfoldEffect, foldGutter, foldKeymap, indentOnInput, syntaxHighlighting } from '@codemirror/language';
import { classHighlighter } from '@lezer/highlight';
import { search, searchKeymap, openSearchPanel, closeSearchPanel, searchPanelOpen } from '@codemirror/search';
import { autocompletion, completeAnyWord, closeBrackets, closeBracketsKeymap, completionKeymap, completionStatus, closeCompletion, startCompletion } from '@codemirror/autocomplete';
import { createMountable } from './core/mountable.js';
import { createInstanceIdentifier } from './core/identifiers.js';
import { loadLanguage } from './code-editor/languages.js';
import { jsonNewline } from './code-editor/json-newline.js';
import { canFormat, formatCode } from './code-editor/formatters.js';
import '../css/code-editor.css';

const origin = Annotation.define();

function initialize(root, configuration) {
    const input = root.querySelector('textarea');
    const host = root.querySelector('[data-code-editor-host]');
    if (!(input instanceof HTMLTextAreaElement) || !(host instanceof HTMLElement)) {
        throw new Error('Code Editor requires a textarea and editor host.');
    }
    const label = typeof configuration.label === 'string' ? configuration.label : 'Code';
    const labels = { line: 'Ln', column: 'Col', readOnly: 'Read only', copied: 'Copied', required: 'Please enter code.', search: 'Search', closeSearch: 'Close search', expand: 'Expand editor', restore: 'Collapse editor', wrap: 'Wrap lines', unwrap: 'Unwrap lines', languageUnavailable: 'The requested language could not be loaded.', clipboardUnavailable: 'Code could not be copied. Select it and copy manually.', format: 'Format code', formatting: 'Formatting code…', formatted: 'Code formatted', formatFailed: 'The code could not be formatted.', ...(configuration.labels ?? {}) };
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
    let formatting = false;
    let view;
    const toolbar = root.querySelector('[data-code-editor-toolbar]');
    const position = root.querySelector('[data-code-editor-position]');
    const feedback = root.querySelector('[data-code-editor-feedback]');
    const languageLabel = root.querySelector('[data-code-editor-language]');
    const controller = new AbortController();
    const backdrop = document.createElement('button');
    backdrop.type = 'button';
    backdrop.className = 'daisy-kit-code-editor__backdrop';
    backdrop.tabIndex = -1;
    backdrop.hidden = true;
    backdrop.setAttribute('aria-label', labels.restore);
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
        const toggles = { wrap: [lineWrapping, 'unwrap', 'wrap'], expand: [expanded, 'restore', 'expand'], search: [searchPanelOpen(view.state), 'closeSearch', 'search'] };
        root.querySelectorAll('[data-code-editor-action]').forEach(button => {
            const action = button.dataset.codeEditorAction;
            button.hidden = (Array.isArray(configuration.toolbarActions) && !configuration.toolbarActions.includes(action)) || (['undo', 'redo', 'complete', 'format'].includes(action) && readOnly);
            button.disabled = disabled() || (action === 'undo' && undoDepth(view.state) === 0) || (action === 'redo' && redoDepth(view.state) === 0) || (action === 'format' && (formatting || !canFormat(language)));
            if (action === 'format') {
                button.setAttribute('aria-busy', String(formatting));
                const text = formatting ? labels.formatting : labels.format;
                if (button.textContent !== text) button.textContent = text;
            }
            if (toggles[action]) {
                const [pressed, on, off] = toggles[action];
                button.setAttribute('aria-pressed', String(pressed));
                button.classList.toggle('btn-active', pressed);
                const text = labels[pressed ? on : off];
                if (button.textContent !== text) button.textContent = text;
            }
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
            EditorState.languageData.of(() => [{ autocomplete: completeAnyWord }]),
            EditorView.cspNonce.of(typeof configuration.nonce === 'string' ? configuration.nonce : ''),
            EditorState.phrases.of(configuration.phrases ?? {}),
            keymap.of([...closeBracketsKeymap, { key: 'Enter', run: editor => language === 'json' && jsonNewline(editor) }, ...defaultKeymap, ...searchKeymap, ...historyKeymap, ...foldKeymap, ...completionKeymap]),
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
    root.before(backdrop);
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
            updateControls();
            clearError('language-unavailable');
            if (languageLabel) languageLabel.textContent = value;
            emit('language-changed', { language });
            return true;
        } catch {
            if (!active || revision !== languageRevision) return false;
            return error('language-unavailable', labels.languageUnavailable);
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
        backdrop.hidden = !value;
        root.classList.toggle('daisy-kit-code-editor--expanded', value);
        document.documentElement.classList.toggle('daisy-kit-code-editor-expanded-page', document.querySelector('.daisy-kit-code-editor--expanded') !== null);
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
        } catch { return active ? error('clipboard-unavailable', labels.clipboardUnavailable) : false; }
    }
    async function format() {
        if (!active || disabled() || readOnly || formatting || !canFormat(language)) return false;
        const document = view.state.doc;
        const selection = view.state.selection;
        const revision = languageRevision;
        const isCurrent = () => active && !disabled() && !readOnly && document === view.state.doc && revision === languageRevision && selection.eq(view.state.selection);
        formatting = true;
        updateControls();
        try {
            const result = await formatCode(document.toString(), language, view.state.facet(EditorState.tabSize), selection.main.head);
            if (!isCurrent()) return false;
            clearError('format-failed');
            if (result.formatted !== document.toString()) {
                view.dispatch({
                    changes: { from: 0, to: document.length, insert: result.formatted },
                    selection: { anchor: Math.max(0, Math.min(result.cursorOffset, result.formatted.length)) },
                    annotations: [origin.of('format'), isolateHistory.of('full')],
                });
            }
            if (feedback) {
                feedback.textContent = labels.formatted;
                clearTimeout(feedbackTimer);
                feedbackTimer = setTimeout(() => { feedback.textContent = ''; }, 2000);
            }
            emit('formatted', { language });
            return true;
        } catch {
            return isCurrent() ? error('format-failed', labels.formatFailed) : false;
        } finally {
            formatting = false;
            if (active) updateControls();
        }
    }
    const command = operation => active && !disabled() && !readOnly ? operation(view) : false;
    const navigation = operation => active && !disabled() ? operation(view) : false;
    const openSearch = () => active && !disabled() ? openSearchPanel(view) : false;
    function foldOtherBlocks(unfold) {
        if (!active || disabled()) return false;
        const { state } = view;
        const head = state.selection.main.head;
        const ranges = [];
        for (let number = 1; number <= state.doc.lines; number++) {
            const line = state.doc.line(number);
            const range = foldable(state, line.from, line.to);
            if (range) ranges.push({ ...range, start: line.from });
        }
        const current = ranges.filter(range => range.start <= head && range.to >= head)
            .sort((left, right) => (left.to - left.start) - (right.to - right.start))[0];
        const isOther = (from, to) => current ? to < current.start || from > current.to : !(from <= head && to >= head);
        const effects = [];
        if (unfold) {
            foldedRanges(state).between(0, state.doc.length, (from, to) => {
                if (isOther(from, to)) effects.push(unfoldEffect.of({ from, to }));
            });
        } else {
            let coveredUntil = -1;
            for (const range of ranges) {
                if (range.from > coveredUntil && isOther(range.start, range.to)) {
                    effects.push(foldEffect.of(range));
                    coveredUntil = range.to;
                }
            }
        }
        if (effects.length) view.dispatch({ effects });
        return effects.length > 0;
    }
    const actions = {
        copy, format, search: () => navigation(searchPanelOpen(view.state) ? closeSearchPanel : openSearchPanel),
        undo: () => command(undo), redo: () => command(redo),
        complete: () => { if (!focus() || readOnly) return false; return startCompletion(view); },
        'fold-all': () => navigation(foldAll), 'unfold-all': () => navigation(unfoldAll),
        'fold-others': () => foldOtherBlocks(false), 'unfold-others': () => foldOtherBlocks(true),
        wrap: () => setLineWrapping(!lineWrapping), expand: () => setExpanded(!expanded),
    };
    root.addEventListener('click', event => {
        if (event.target.closest('[data-code-editor-minimize]')) { setExpanded(false); return; }
        const button = event.target.closest('[data-code-editor-action]');
        if (button && root.contains(button) && !button.disabled) actions[button.dataset.codeEditorAction]?.();
    }, { signal: controller.signal });
    backdrop.addEventListener('click', () => setExpanded(false), { signal: controller.signal });
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
        undo: actions.undo, redo: actions.redo, openSearch, copy, setExpanded, format,
        complete: actions.complete, foldAll: actions['fold-all'], unfoldAll: actions['unfold-all'], foldOthers: actions['fold-others'], unfoldOthers: actions['unfold-others'],
        destroy() {
            if (expanded) setExpanded(false);
            active = false;
            languageRevision += 1;
            controller.abort();
            backdrop.remove();
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
