import '../../css/forms-builder.css';
import { createMountable, installLivewireAdapter as createLivewireAdapter } from '../core/mountable.js';

function emit(root, name, detail = {}) {
    root.dispatchEvent(new CustomEvent(`daisy-kit:forms-builder:${name}`, { bubbles: true, detail }));
}

function setStatus(root, message, state) {
    const status = root.querySelector('[data-daisy-kit-status]');
    root.dataset.daisyKitState = state;
    root.setAttribute('aria-busy', 'false');

    if (status) {
        status.textContent = message;
        status.hidden = message === '';
    }
}

function initialize(root, configuration) {
    if (configuration.livewireAvailable !== true) {
        setStatus(root, 'Forms Builder authoring requires optional Livewire 4.', 'empty');
        emit(root, 'unavailable', { reason: 'livewire-unavailable' });

        return () => {};
    }

    const livewireBuilder = root.querySelector(':scope > [data-daisy-kit-livewire-builder]');

    if (!(livewireBuilder instanceof HTMLElement)) {
        setStatus(root, 'The Livewire form builder mount point is unavailable.', 'error');
        emit(root, 'error', { reason: 'missing-livewire-content' });

        return () => {};
    }

    // Livewire owns authoring markup and mutations. The ESM entry solely provides
    // the standard package lifecycle around that server-rendered implementation.
    setStatus(root, '', 'ready');

    return () => {};
}

const module = createMountable('forms-builder', initialize);

export const { mount, mountAll, unmount } = module;

export function installLivewireAdapter() {
    return createLivewireAdapter('forms-builder', mountAll, unmount);
}
