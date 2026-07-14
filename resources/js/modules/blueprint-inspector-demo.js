function field(root, name) {
    return root.querySelector(`[data-blueprint-demo-field="${name}"]`);
}

function ensureSelectValue(select, value) {
    if (!select || !value) {
        return;
    }

    if (![...select.options].some(option => option.value === value)) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = value;
        select.append(option);
    }

    select.value = value;
}

export default function initBlueprintInspectorDemo(root) {
    const blueprint = root.closest('[data-blueprint]');
    if (!blueprint) {
        return null;
    }

    const nodeFields = root.querySelector('[data-blueprint-demo-node-fields]');
    const transitionFields = root.querySelector('[data-blueprint-demo-transition-fields]');
    let context = null;
    let value = null;

    function readValue() {
        const data = { ...(value?.data ?? {}) };
        const isNode = context?.selection.type === 'node';

        if (isNode) {
            data.owner = field(root, 'owner')?.value ?? '';
            data.priority = field(root, 'priority')?.value ?? '';
            data.expedited = field(root, 'expedited')?.checked === true;
        } else {
            data.notify = field(root, 'notify')?.checked === true;
        }

        return {
            label: field(root, 'label')?.value ?? '',
            description: field(root, 'description')?.value ?? '',
            category: field(root, 'category')?.value ?? '',
            data,
        };
    }

    function hydrate(event) {
        context = event.detail;
        value = {
            ...context.value,
            data: { ...context.value.data },
        };
        field(root, 'label').value = value.label;
        field(root, 'description').value = value.description;
        ensureSelectValue(field(root, 'category'), value.category);
        field(root, 'owner').value = value.data.owner ?? '';
        ensureSelectValue(field(root, 'priority'), value.data.priority ?? 'normal');
        field(root, 'expedited').checked = value.data.expedited === true;
        field(root, 'notify').checked = value.data.notify === true;

        const isNode = context.selection.type === 'node';
        nodeFields.hidden = !isNode;
        transitionFields.hidden = isNode;
    }

    function updateDraft(event) {
        if (!context || !event.target.closest('[data-blueprint-demo-field]')) {
            return;
        }

        context.setDraft(readValue());
    }

    function handleAction(event) {
        const action = event.target.closest('[data-blueprint-demo-action]')?.dataset.blueprintDemoAction;
        if (!action || !context) {
            return;
        }

        if (action === 'save') {
            context.commit(readValue());
            return;
        }

        if (action === 'cancel') {
            context.cancel('integrator');
            return;
        }

        if (action === 'delete') {
            const { type, id } = context.selection;
            type === 'node'
                ? blueprint.__daisyBlueprint?.removeNode(id)
                : blueprint.__daisyBlueprint?.removeTransition(id);
        }
    }

    function clearContext() {
        context = null;
        value = null;
    }

    blueprint.addEventListener('daisy:blueprint:inspector-open', hydrate);
    blueprint.addEventListener('daisy:blueprint:inspector-commit', clearContext);
    blueprint.addEventListener('daisy:blueprint:inspector-cancel', clearContext);
    root.addEventListener('input', updateDraft);
    root.addEventListener('change', updateDraft);
    root.addEventListener('click', handleAction);

    return {
        destroy() {
            blueprint.removeEventListener('daisy:blueprint:inspector-open', hydrate);
            blueprint.removeEventListener('daisy:blueprint:inspector-commit', clearContext);
            blueprint.removeEventListener('daisy:blueprint:inspector-cancel', clearContext);
            root.removeEventListener('input', updateDraft);
            root.removeEventListener('change', updateDraft);
            root.removeEventListener('click', handleAction);
        },
    };
}
