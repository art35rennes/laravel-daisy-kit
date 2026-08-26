const modules = [
    ['forms-viewer', async () => {
        await import('../../../dist/forms-viewer.css');

        return import('../../../dist/forms-viewer.js');
    }],
    ['forms-builder', async () => {
        await import('../../../dist/forms-builder.css');

        return import('../../../dist/forms-builder.js');
    }],
    ['table', async () => {
        await import('../../../dist/table.css');

        return import('../../../dist/table.js');
    }],
    ['tree', async () => {
        await import('../../../dist/tree.css');

        return import('../../../dist/tree.js');
    }],
    ['blueprint', async () => {
        await import('../../../dist/blueprint.css');

        return import('../../../dist/blueprint.js');
    }],
    ['file-preview', async () => {
        await import('../../../dist/file-preview.css');

        return import('../../../dist/file-preview.js');
    }],
    ['map', async () => {
        await import('../../../dist/map.css');

        return import('../../../dist/map.js');
    }],
];

modules.forEach(async ([moduleName, loadModule]) => {
    const module = await loadModule();

    module.mountAll(document.querySelector(`[data-daisy-kit-module="${moduleName}"]`)?.parentElement ?? document);
});
