import { createControls } from './controls.js';
import { categoryFor, mergeDefaults } from './schema.js';

const AUTOSAVE_DELAY = 350;

/**
 * Clones JSON-compatible values.
 *
 * @param {*} value - Value to clone.
 * @returns {*} Cloned value.
 */
function cloneValue(value) {
    return JSON.parse(JSON.stringify(value ?? {}));
}

/**
 * Opens a native dialog with a resilient fallback.
 *
 * @param {HTMLDialogElement|null} dialog - Dialog to open.
 * @returns {void}
 */
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

/**
 * Closes a native dialog with a resilient fallback.
 *
 * @param {HTMLDialogElement|null} dialog - Dialog to close.
 * @returns {void}
 */
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

/**
 * Populates a category select.
 *
 * @param {HTMLSelectElement} select - Category select.
 * @param {Array<object>} categories - Normalized categories.
 * @param {string} currentValue - Current category.
 * @returns {void}
 */
function fillCategories(select, categories, currentValue) {
    const fragment = document.createDocumentFragment();
    const empty = document.createElement('option');
    empty.value = '';
    fragment.append(empty);

    categories.forEach((category) => {
        const option = document.createElement('option');
        option.value = category.value;
        option.textContent = category.label;
        fragment.append(option);
    });

    if (currentValue && !categories.some(category => category.value === currentValue)) {
        const option = document.createElement('option');
        option.value = currentValue;
        option.textContent = currentValue;
        fragment.append(option);
    }

    select.replaceChildren(fragment);
    select.value = currentValue ?? '';
}

/**
 * Creates the Blueprint inspector state machine.
 *
 * @param {HTMLElement} root - Blueprint root.
 * @param {object} options - Inspector options.
 * @param {boolean} options.autosave - Whether drafts save automatically.
 * @param {Function} options.onCommit - Commit callback.
 * @param {Function} [options.onError] - Validation error callback.
 * @param {boolean} [options.enhanceRichControls] - Whether rich controls should initialize.
 * @returns {object} Inspector controller.
 */
export function createInspector(root, {
    autosave,
    onCommit,
    onError = () => {},
    enhanceRichControls = true,
}) {
    const inspector = root.querySelector('[data-blueprint-inspector]');
    const backdrop = root.querySelector('[data-blueprint-inspector-backdrop]');
    const inspectorMode = root.dataset.inspectorMode === 'modal' ? 'modal' : 'sidebar';
    const usesDialog = inspectorMode === 'modal' || inspector?.tagName === 'DIALOG';
    const form = root.querySelector('[data-blueprint-inspector-form]');
    const title = root.querySelector('[data-blueprint-inspector-title]');
    const dirtyIndicator = root.querySelector('[data-blueprint-dirty-indicator]');
    const fieldsContainer = root.querySelector('[data-blueprint-integrator-fields]');
    const discardDialog = root.querySelector('[data-blueprint-discard-dialog]');
    const keepEditingButton = root.querySelector('[data-blueprint-action="keep-editing"]');
    const discardButton = root.querySelector('[data-blueprint-action="discard-changes"]');
    let selection = null;
    let entity = null;
    let categories = [];
    let controls = null;
    let dirty = false;
    let pendingAction = null;
    let autosaveTimer = null;

    /**
     * Updates the visible dirty state.
     *
     * @param {boolean} value - Dirty state.
     * @returns {void}
     */
    function setDirty(value) {
        dirty = value;

        if (inspector) {
            inspector.dataset.dirty = value ? 'true' : 'false';
        }

        dirtyIndicator?.classList.toggle('hidden', !value);
    }

    /**
     * Returns the current data draft.
     *
     * @param {string} category - Target category.
     * @returns {object} Draft with category defaults.
     */
    function dataDraft(category) {
        const current = controls?.read() ?? entity?.data ?? {};
        const defaults = categoryFor(categories, category)?.defaults ?? {};

        return mergeDefaults(current, defaults);
    }

    /**
     * Renders fields for the current category.
     *
     * @param {string} category - Category value.
     * @param {object} data - Current integrator data.
     * @returns {void}
     */
    function renderFields(category, data) {
        controls?.destroy();
        const fields = categoryFor(categories, category)?.fields ?? [];
        controls = createControls(fieldsContainer, fields, data, {
            onInput: markDirty,
            enhanceRichControls,
        });
    }

    /**
     * Commits the current inspector draft.
     *
     * @returns {boolean} Whether the draft was committed.
     */
    function save() {
        if (!selection || !entity || !form) {
            return false;
        }

        const collection = selection.type === 'node' ? 'nodes' : 'transitions';
        const invalidField = controls?.invalidField();
        if (invalidField) {
            form.reportValidity?.();
            onError(new Error('invalid_integrator_field'), {
                path: `${collection}.${selection.id}.data.${invalidField}`,
                selection: cloneValue(selection),
            });

            return false;
        }

        if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
            form.reportValidity?.();
            onError(new Error('invalid_integrator_field'), {
                path: `${collection}.${selection.id}`,
                selection: cloneValue(selection),
            });

            return false;
        }

        const changes = {
            label: form.elements.label.value,
            description: form.elements.description.value,
            category: form.elements.category.value,
            data: dataDraft(form.elements.category.value),
        };
        onCommit(cloneValue(selection), changes);
        entity = { ...entity, ...cloneValue(changes) };
        setDirty(false);

        return true;
    }

    /**
     * Schedules autosave for the current draft.
     *
     * @returns {void}
     */
    function scheduleAutosave() {
        window.clearTimeout(autosaveTimer);

        if (autosave) {
            autosaveTimer = window.setTimeout(save, AUTOSAVE_DELAY);
        }
    }

    /**
     * Marks the draft dirty and schedules autosave.
     *
     * @returns {void}
     */
    function markDirty() {
        setDirty(true);
        scheduleAutosave();
    }

    /**
     * Handles generic field changes, including category switches.
     *
     * @param {Event} event - Form input event.
     * @returns {void}
     */
    function onFormInput(event) {
        if (event.target === form.elements.category) {
            const data = dataDraft(form.elements.category.value);
            renderFields(form.elements.category.value, data);
        }

        markDirty();
    }

    /**
     * Handles explicit inspector saves.
     *
     * @param {SubmitEvent} event - Submit event.
     * @returns {void}
     */
    function onSubmit(event) {
        event.preventDefault();
        window.clearTimeout(autosaveTimer);
        save();
    }

    /**
     * Keeps editing after a discard prompt.
     *
     * @returns {void}
     */
    function keepEditing() {
        pendingAction = null;
        closeDialog(discardDialog);
    }

    /**
     * Discards the current draft and runs the pending action.
     *
     * @returns {void}
     */
    function discardChanges() {
        const action = pendingAction;
        pendingAction = null;
        window.clearTimeout(autosaveTimer);
        closeDialog(discardDialog);
        setDirty(false);
        action?.();
    }

    /**
     * Routes native Escape requests through the normal close action so dirty
     * inspector drafts receive the same discard confirmation as a click.
     *
     * @param {Event} event - Native dialog cancel event.
     * @returns {void}
     */
    function onInspectorCancel(event) {
        event.preventDefault();
        inspector?.querySelector('[data-blueprint-action="close-inspector"]')?.click();
    }

    form?.addEventListener('input', onFormInput);
    form?.addEventListener('change', onFormInput);
    form?.addEventListener('submit', onSubmit);
    keepEditingButton?.addEventListener('click', keepEditing);
    discardButton?.addEventListener('click', discardChanges);
    inspector?.addEventListener('cancel', onInspectorCancel);

    return {
        /**
         * Opens the inspector for a workflow entity.
         *
         * @param {object} context - Selection context.
         * @returns {void}
         */
        open({ selection: nextSelection, entity: nextEntity, categories: nextCategories, titlePrefix }) {
            selection = cloneValue(nextSelection);
            entity = cloneValue(nextEntity);
            categories = nextCategories;
            fillCategories(form.elements.category, categories, entity.category);
            form.elements.label.value = entity.label;
            form.elements.description.value = entity.description;
            title.textContent = `${titlePrefix}: ${entity.label}`;
            renderFields(entity.category, mergeDefaults(
                entity.data,
                categoryFor(categories, entity.category)?.defaults ?? {},
            ));
            inspector.classList.remove('hidden');
            if (usesDialog) {
                openDialog(inspector);
            } else {
                backdrop?.classList.remove('hidden');
            }
            setDirty(false);
        },

        /**
         * Guards an action when the current draft is dirty.
         *
         * @param {Function} action - Deferred action.
         * @returns {boolean} Whether the action ran immediately.
         */
        request(action) {
            if (!dirty || autosave) {
                action();

                return true;
            }

            pendingAction = action;
            openDialog(discardDialog);

            return false;
        },

        /**
         * Closes and clears the inspector.
         *
         * @returns {void}
         */
        close() {
            window.clearTimeout(autosaveTimer);
            controls?.destroy();
            controls = null;
            selection = null;
            entity = null;
            inspector?.classList.add('hidden');
            if (usesDialog) {
                closeDialog(inspector);
            } else {
                backdrop?.classList.add('hidden');
            }
            setDirty(false);
        },

        /**
         * Returns whether the current draft differs from the workflow.
         *
         * @returns {boolean} Dirty state.
         */
        isDirty() {
            return dirty;
        },

        /**
         * Removes inspector listeners and dynamic controls.
         *
         * @returns {void}
         */
        destroy() {
            window.clearTimeout(autosaveTimer);
            controls?.destroy();
            form?.removeEventListener('input', onFormInput);
            form?.removeEventListener('change', onFormInput);
            form?.removeEventListener('submit', onSubmit);
            keepEditingButton?.removeEventListener('click', keepEditing);
            discardButton?.removeEventListener('click', discardChanges);
            inspector?.removeEventListener('cancel', onInspectorCancel);
        },
    };
}
