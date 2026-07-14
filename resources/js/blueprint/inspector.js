function cloneValue(value) {
    return JSON.parse(JSON.stringify(value));
}

function isPlainObject(value) {
    return value !== null && typeof value === 'object' && !Array.isArray(value);
}

function inspectorPath(selection, suffix = '') {
    const collection = selection?.type === 'transition' ? 'transitions' : 'nodes';
    const base = `${collection}.${selection?.id ?? ''}`;

    return suffix ? `${base}.${suffix}` : base;
}

function normalizeInspectorValue(value, fallbackData) {
    if (!isPlainObject(value)) {
        throw new Error('invalid_inspector_value');
    }

    const data = Object.prototype.hasOwnProperty.call(value, 'data')
        ? value.data
        : fallbackData;

    if (!isPlainObject(data)) {
        throw new Error('invalid_inspector_data');
    }

    if (![value.label, value.description, value.category].every(item => typeof item === 'string')) {
        throw new Error('invalid_inspector_value');
    }

    try {
        return cloneValue({
            label: value.label,
            description: value.description,
            category: value.category,
            data,
        });
    } catch {
        throw new Error('invalid_inspector_data');
    }
}

function valuesMatch(first, second) {
    return JSON.stringify(first) === JSON.stringify(second);
}

function openDialog(dialog) {
    if (!dialog) {
        return;
    }

    if (typeof dialog.showModal === 'function' && !dialog.open) {
        dialog.showModal();

        return;
    }

    dialog.setAttribute('open', '');
}

function closeDialog(dialog) {
    if (!dialog) {
        return;
    }

    if (typeof dialog.close === 'function' && dialog.open) {
        dialog.close();

        return;
    }

    dialog.removeAttribute('open');
}

export function createInspector(root, {
    onOpen = () => {},
    onCommit,
    onCancel = () => {},
    onError = () => {},
}) {
    const inspector = root.querySelector('[data-blueprint-inspector]');
    const content = root.querySelector('[data-blueprint-inspector-content]');
    const title = root.querySelector('[data-blueprint-inspector-title]');
    const dirtyIndicator = root.querySelector('[data-blueprint-dirty-indicator]');
    const discardDialog = root.querySelector('[data-blueprint-discard-dialog]');
    const keepEditingButton = root.querySelector('[data-blueprint-action="keep-editing"]');
    const discardButton = root.querySelector('[data-blueprint-action="discard-changes"]');
    let selection = null;
    let initialValue = null;
    let draft = null;
    let isNew = false;
    let dirty = false;
    let session = 0;
    let pendingAction = null;
    let pendingReason = 'cancel';

    function setDirty(value) {
        dirty = value;

        if (inspector) {
            inspector.dataset.dirty = value ? 'true' : 'false';
        }

        dirtyIndicator?.classList.toggle('hidden', !value);
    }

    function reportInvalid(error) {
        onError(error, {
            path: inspectorPath(selection, error.message === 'invalid_inspector_data' ? 'data' : ''),
            selection: selection ? cloneValue(selection) : null,
        });
    }

    function updateDraft(value, expectedSession = session) {
        if (!selection || expectedSession !== session) {
            return null;
        }

        try {
            draft = normalizeInspectorValue(value, initialValue?.data);
            setDirty(!valuesMatch(draft, initialValue) || isNew);

            return cloneValue(draft);
        } catch (error) {
            reportInvalid(error);

            return null;
        }
    }

    function close() {
        selection = null;
        initialValue = null;
        draft = null;
        isNew = false;
        pendingAction = null;
        setDirty(false);
        inspector?.classList.add('hidden');
        closeDialog(inspector);
    }

    function finishCancel(reason, expectedSession = session) {
        if (!selection || expectedSession !== session) {
            return false;
        }

        const cancelledSelection = cloneValue(selection);
        const cancelledValue = cloneValue(initialValue);
        const cancelledDraft = cloneValue(draft);
        const cancelledIsNew = isNew;
        close();
        onCancel(cancelledSelection, cancelledValue, cancelledDraft, {
            isNew: cancelledIsNew,
            reason,
        });

        return true;
    }

    function request(action, reason = 'selection') {
        if (!selection) {
            action();

            return true;
        }

        if (!dirty) {
            finishCancel(reason);
            action();

            return true;
        }

        pendingAction = action;
        pendingReason = reason;
        openDialog(discardDialog);

        return false;
    }

    function cancel(reason = 'cancel', expectedSession = session) {
        if (!selection || expectedSession !== session) {
            return false;
        }

        return request(() => {}, reason);
    }

    function commit(value, expectedSession = session) {
        if (!selection || expectedSession !== session) {
            return null;
        }

        const nextDraft = value === undefined ? cloneValue(draft) : updateDraft(value, expectedSession);
        if (!nextDraft) {
            return null;
        }

        try {
            const committedSelection = cloneValue(selection);
            const committedIsNew = isNew;
            const result = onCommit(committedSelection, cloneValue(nextDraft), { isNew: committedIsNew });
            close();

            return result ?? cloneValue(nextDraft);
        } catch (error) {
            onError(error, {
                path: inspectorPath(selection),
                selection: cloneValue(selection),
            });

            return null;
        }
    }

    function keepEditing() {
        pendingAction = null;
        closeDialog(discardDialog);
    }

    function discardChanges() {
        const action = pendingAction;
        const reason = pendingReason;
        pendingAction = null;
        closeDialog(discardDialog);
        finishCancel(reason);
        action?.();
    }

    function onInspectorCancel(event) {
        event.preventDefault();
        cancel('escape');
    }

    keepEditingButton?.addEventListener('click', keepEditing);
    discardButton?.addEventListener('click', discardChanges);
    inspector?.addEventListener('cancel', onInspectorCancel);

    return {
        open({ selection: nextSelection, value, title: nextTitle = '', isNew: nextIsNew = false }) {
            let nextValue;

            try {
                nextValue = normalizeInspectorValue(value);
            } catch (error) {
                selection = cloneValue(nextSelection);
                reportInvalid(error);
                selection = null;

                return null;
            }

            session += 1;
            selection = cloneValue(nextSelection);
            initialValue = nextValue;
            draft = cloneValue(nextValue);
            isNew = nextIsNew;
            title.textContent = nextTitle;
            inspector.classList.remove('hidden');
            openDialog(inspector);
            setDirty(isNew);

            const currentSession = session;
            const context = {
                selection: cloneValue(selection),
                value: cloneValue(initialValue),
                isNew,
                setDraft(nextDraft) {
                    return updateDraft(nextDraft, currentSession);
                },
                commit(nextDraft) {
                    return commit(nextDraft, currentSession);
                },
                cancel(reason = 'cancel') {
                    return cancel(reason, currentSession);
                },
            };
            onOpen({ ...context, value: cloneValue(context.value), selection: cloneValue(context.selection) });
            content?.querySelector('[autofocus], input, textarea, select, button')?.focus({ preventScroll: true });

            return context;
        },
        setDraft: updateDraft,
        getDraft() {
            return draft ? cloneValue(draft) : null;
        },
        commit,
        cancel,
        request,
        close,
        isDirty() {
            return dirty;
        },
        destroy() {
            keepEditingButton?.removeEventListener('click', keepEditing);
            discardButton?.removeEventListener('click', discardChanges);
            inspector?.removeEventListener('cancel', onInspectorCancel);
            close();
        },
    };
}
