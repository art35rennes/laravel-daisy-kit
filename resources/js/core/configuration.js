export function readConfiguration(root) {
    const node = root.querySelector(':scope > script[data-daisy-kit-config]');

    if (!node || node.type !== 'application/json') {
        return { error: 'missing-configuration', value: null };
    }

    try {
        const value = JSON.parse(node.textContent ?? '');

        if (!value || Array.isArray(value) || typeof value !== 'object') {
            return { error: 'invalid-configuration', value: null };
        }

        return { error: null, value };
    } catch {
        return { error: 'invalid-configuration', value: null };
    }
}

export function showConfigurationError(root) {
    showError(root, 'This module configuration is invalid.');
}

export function showError(root, message) {
    root.dataset.daisyKitState = 'error';

    const status = root.querySelector('[data-daisy-kit-status]');

    if (status) {
        status.hidden = false;
        status.textContent = message;
    }
}
