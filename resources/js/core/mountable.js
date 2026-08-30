import { readConfiguration, showConfigurationError, showError } from './configuration.js';

export function createMountable(moduleName, initialize) {
    const instances = new WeakMap();

    function mount(root) {
        if (!(root instanceof Element)) {
            throw new TypeError('Daisy Kit modules mount Element roots only.');
        }

        if (instances.has(root)) {
            return instances.get(root).facade;
        }

        const { error, value } = readConfiguration(root);

        if (error) {
            showConfigurationError(root);
            root.dispatchEvent(new CustomEvent(`daisy-kit:${moduleName}:error`, {
                bubbles: true,
                detail: {
                    code: error,
                    message: 'This module configuration is invalid.',
                },
            }));

            return null;
        }

        let initialized;

        try {
            initialized = initialize(root, value);
        } catch (error) {
            showError(root, 'This module could not be initialized.');
            root.dispatchEvent(new CustomEvent(`daisy-kit:${moduleName}:error`, {
                bubbles: true,
                detail: {
                    code: 'initialization-failed',
                    message: error instanceof Error && error.message !== ''
                        ? error.message
                        : 'An unknown initialization failure occurred.',
                },
            }));

            return null;
        }
        if (initialized === null || initialized === undefined) {
            return null;
        }

        const initializedInstance = typeof initialized === 'function'
            ? { destroy: initialized }
            : { ...initialized };
        const destroy = typeof initializedInstance.destroy === 'function'
            ? initializedInstance.destroy
            : () => {};
        delete initializedInstance.destroy;
        const instance = { destroy, facade: initializedInstance };

        instances.set(root, instance);
        if (!root.dataset.daisyKitState) {
            root.dataset.daisyKitState = 'ready';
        }
        root.dispatchEvent(new CustomEvent(`daisy-kit:${moduleName}:mounted`, { bubbles: true, detail: {} }));

        return instance.facade;
    }

    function mountAll(scope = document) {
        return [...scope.querySelectorAll(`[data-daisy-kit-module="${moduleName}"]`)].map(mount);
    }

    function unmount(root) {
        const instance = instances.get(root);

        if (!instance) {
            return false;
        }

        instance.destroy();
        instances.delete(root);
        delete root.dataset.daisyKitState;
        root.dispatchEvent(new CustomEvent(`daisy-kit:${moduleName}:unmounted`, { bubbles: true, detail: {} }));

        return true;
    }

    function getInstance(root) {
        return instances.get(root)?.facade ?? null;
    }

    return { getInstance, mount, mountAll, unmount };
}
