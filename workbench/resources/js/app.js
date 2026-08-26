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

    module.mountAll(document.querySelector(`[data-daisy-kit-module="${moduleName}"]`)?.parentElement ?? document);
});
