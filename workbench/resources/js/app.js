const modules = [
    ['forms-viewer', async () => {
        await import('@daisy-kit/forms-viewer.css');

        return import('@daisy-kit/forms-viewer.js');
    }],
    ['forms-builder', async () => {
        await import('@daisy-kit/forms-builder.css');

        return import('@daisy-kit/forms-builder.js');
    }],
    ['table', async () => {
        await import('@daisy-kit/table.css');

        return import('@daisy-kit/table.js');
    }],
    ['tree', async () => {
        await import('@daisy-kit/tree.css');

        return import('@daisy-kit/tree.js');
    }],
    ['blueprint', async () => {
        await import('@daisy-kit/blueprint.css');

        return import('@daisy-kit/blueprint.js');
    }],
    ['file-preview', async () => {
        await import('@daisy-kit/file-preview.css');

        return import('@daisy-kit/file-preview.js');
    }],
    ['map', async () => {
        await import('@daisy-kit/map.css');

        return import('@daisy-kit/map.js');
    }],
];

modules.forEach(async ([moduleName, loadModule]) => {
    const module = await loadModule();

    // A Livewire Builder preview contains a second Viewer root. Each independent
    // entry must discover every root it owns, rather than only the first root's
    // section wrapper.
    module.mountAll();

    if (moduleName === 'map') {
        document.querySelectorAll('[data-workbench-map-action]').forEach((button) => {
            button.addEventListener('click', () => {
                const root = button.closest('[data-daisy-kit-module="map"]');
                const instance = module.getInstance(root);

                if (!instance) return;

                if (button.dataset.workbenchMapAction === 'view') {
                    instance.setView([48.1512, -1.6842], 14);
                    root.dataset.workbenchFacade = 'view-updated';
                }

                if (button.dataset.workbenchMapAction === 'invalidate') {
                    instance.invalidateSize();
                    root.dataset.workbenchFacade = 'layout-updated';
                }
            });
        });
    }
});
