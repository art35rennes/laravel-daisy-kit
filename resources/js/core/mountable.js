import { readConfiguration, showConfigurationError, showError } from './configuration.js';

export function createMountable(moduleName, initialize) {
    const instances = new WeakMap();

    function mount(root) {
        if (!(root instanceof Element)) {
            throw new TypeError('Daisy Kit modules mount Element roots only.');
        }

        if (instances.has(root)) {
            return instances.get(root);
        }

        const { error, value } = readConfiguration(root);

        if (error) {
            showConfigurationError(root);

            return null;
        }

        let destroy;

        try {
            destroy = initialize(root, value) ?? (() => {});
        } catch {
            showError(root, 'This module could not be initialized.');
            root.dispatchEvent(new CustomEvent(`daisy-kit:${moduleName}:error`, { bubbles: true }));

            return null;
        }
        const instance = { destroy };

        instances.set(root, instance);
        if (!root.dataset.daisyKitState) {
            root.dataset.daisyKitState = 'ready';
        }
        root.dispatchEvent(new CustomEvent(`daisy-kit:${moduleName}:mounted`, { bubbles: true }));

        return instance;
    }

    function mountAll(scope = document) {
        return [...scope.querySelectorAll(`[data-daisy-kit-module="${moduleName}"]`)].map(mount);
    }

    function unmount(root) {
        const instance = instances.get(root);

        if (!instance) {
            return;
        }

        instance.destroy();
        instances.delete(root);
        delete root.dataset.daisyKitState;
        root.dispatchEvent(new CustomEvent(`daisy-kit:${moduleName}:unmounted`, { bubbles: true }));
    }

    return { mount, mountAll, unmount };
}

export function installLivewireAdapter(moduleName, mountAll, unmount) {
    const handler = () => {
        document.querySelectorAll(`[data-daisy-kit-module="${moduleName}"]`).forEach((root) => {
            unmount(root);
            mountAll(root.parentElement ?? document);
        });
    };

    document.addEventListener('livewire:navigated', handler);

    return () => document.removeEventListener('livewire:navigated', handler);
}
