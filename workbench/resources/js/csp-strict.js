const modules = [
    ['table', () => import('@daisy-kit/table.js')],
    ['tree', () => import('@daisy-kit/tree.js')],
    ['blueprint', () => import('@daisy-kit/blueprint.js')],
    ['file-preview', () => import('@daisy-kit/file-preview.js')],
    ['map', () => import('@daisy-kit/map.js')],
    ['copyable', () => import('@daisy-kit/copyable.js')],
    ['combobox', () => import('@daisy-kit/combobox.js')],
    ['truncate', () => import('@daisy-kit/truncate.js')],
    ['scrollspy', () => import('@daisy-kit/scrollspy.js')],
];

await Promise.all([
    import('@daisy-kit/table.css'),
    import('@daisy-kit/tree.css'),
    import('@daisy-kit/blueprint.css'),
    import('@daisy-kit/file-preview.css'),
    import('@daisy-kit/map.css'),
    import('@daisy-kit/copyable.css'),
    import('@daisy-kit/combobox.css'),
    import('@daisy-kit/truncate.css'),
    import('@daisy-kit/scrollspy.css'),
]);

await Promise.all(modules.map(async ([, loadModule]) => {
    const module = await loadModule();

    module.mountAll();
}));
