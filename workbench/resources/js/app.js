const modules = [
    ['wysiwyg', async () => {
        await import('@daisy-kit/wysiwyg.css');
        return import('@daisy-kit/wysiwyg.js');
    }],
    ['code-editor', async () => {
        await import('@daisy-kit/code-editor.css');
        return import('@daisy-kit/code-editor.js');
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

const moduleName = document.body.dataset.workbenchModule;
const loadModule = modules.find(([name]) => name === moduleName)?.[1];

if (loadModule) {
    const module = await loadModule();

    module.mountAll();

    if (moduleName === 'wysiwyg') {
        document.addEventListener('daisy-kit:wysiwyg:attachment-add', (event) => {
            const editor = module.getInstance(event.target);
            if (!(event.detail.file instanceof File)) return;

            editor?.setAttachmentProgress(event.detail.id, 45);
            editor?.resolveAttachment(event.detail.id, {
                url: '/_daisy-kit-test/files/preview.svg',
            });
        });
    }

    if (moduleName === 'map') {
        const controlledMap = document.querySelector('#map-controlled');

        controlledMap?.addEventListener('daisy-kit:map:action', (event) => {
            if (event.detail.id !== 'focus-depot') return;

            module.getInstance(controlledMap)?.setView([48.1181, -1.6769], 14);
        });
    }
}
