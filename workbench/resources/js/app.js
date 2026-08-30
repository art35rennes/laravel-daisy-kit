const modules = [
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
    ['copyable', async () => {
        await import('@daisy-kit/copyable.css');
        return import('@daisy-kit/copyable.js');
    }],
    ['combobox', async () => {
        await import('@daisy-kit/combobox.css');
        return import('@daisy-kit/combobox.js');
    }],
    ['signature', async () => {
        await import('@daisy-kit/signature.css');
        return import('@daisy-kit/signature.js');
    }],
    ['truncate', async () => {
        await import('@daisy-kit/truncate.css');
        return import('@daisy-kit/truncate.js');
    }],
    ['scrollspy', async () => {
        await import('@daisy-kit/scrollspy.css');
        return import('@daisy-kit/scrollspy.js');
    }],
    ['transfer-list', async () => {
        await import('@daisy-kit/transfer-list.css');
        return import('@daisy-kit/transfer-list.js');
    }],
];

modules.forEach(async ([, loadModule]) => {
    const module = await loadModule();

    module.mountAll();
});
