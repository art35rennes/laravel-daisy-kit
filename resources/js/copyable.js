import '../css/copyable.css';
import { createMountable } from './core/mountable.js';

function emit(root, name, detail) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:copyable:${name}`, { bubbles: true, detail }));
}

function label(configuration, key, fallback) {
    return typeof configuration[key] === 'string' && configuration[key] !== ''
        ? configuration[key]
        : fallback;
}

function duration(configuration) {
    return Number.isSafeInteger(configuration.feedbackDuration) && configuration.feedbackDuration >= 0
        ? configuration.feedbackDuration
        : 1000;
}

function initialize(root, configuration) {
    const button = root.querySelector('[data-daisy-kit-copyable-button]');
    const status = root.querySelector('[data-daisy-kit-status]');

    if (!(button instanceof HTMLButtonElement)) {
        throw new Error('The copyable module is missing its button.');
    }

    const original = {
        ariaLabel: button.getAttribute('aria-label'),
        disabled: button.disabled,
        statusHidden: status?.hidden,
        statusText: status?.textContent ?? '',
    };
    const copyLabel = label(configuration, 'copyLabel', 'Copy');
    const successLabel = label(configuration, 'successLabel', 'Copied.');
    const errorLabel = label(configuration, 'errorLabel', 'Copying failed.');
    const configuredValue = typeof configuration.value === 'string' ? configuration.value : null;
    const feedbackDuration = duration(configuration);
    let feedbackTimer = null;

    button.setAttribute('aria-label', copyLabel);
    button.disabled = configuration.disabled === true;

    function getValue() {
        return configuredValue ?? button.textContent?.trim() ?? '';
    }

    function announce(message) {
        if (!status) {
            return;
        }

        window.clearTimeout(feedbackTimer);
        status.hidden = false;
        status.textContent = message;
        feedbackTimer = window.setTimeout(() => {
            status.hidden = true;
            status.textContent = '';
        }, feedbackDuration);
    }

    async function copy(value) {
        const text = typeof value === 'string' ? value : getValue();

        if (button.disabled) {
            announce(errorLabel);
            emit(root, 'error', { code: 'disabled', message: 'Copying is disabled.', value: text });

            return false;
        }

        if (text === '') {
            announce(errorLabel);
            emit(root, 'error', { code: 'empty-value', message: 'There is no text to copy.', value: text });

            return false;
        }

        if (typeof navigator.clipboard?.writeText !== 'function') {
            announce(errorLabel);
            emit(root, 'error', {
                code: 'clipboard-unavailable',
                message: 'The Clipboard API is unavailable.',
                value: text,
            });

            return false;
        }

        try {
            await navigator.clipboard.writeText(text);
            announce(successLabel);
            emit(root, 'copied', { value: text });

            return true;
        } catch {
            announce(errorLabel);
            emit(root, 'error', {
                code: 'clipboard-rejected',
                message: 'The clipboard rejected the copy request.',
                value: text,
            });

            return false;
        }
    }

    function handleClick() {
        void copy();
    }

    button.addEventListener('click', handleClick);

    return {
        copy,
        getValue,
        destroy() {
            window.clearTimeout(feedbackTimer);
            button.removeEventListener('click', handleClick);
            button.disabled = original.disabled;

            if (original.ariaLabel === null) {
                button.removeAttribute('aria-label');
            } else {
                button.setAttribute('aria-label', original.ariaLabel);
            }

            if (status) {
                status.hidden = original.statusHidden ?? true;
                status.textContent = original.statusText;
            }
        },
    };
}

const module = createMountable('copyable', initialize);

export const { getInstance, mount, mountAll, unmount } = module;
