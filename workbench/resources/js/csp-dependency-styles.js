await Promise.all([
    import('@daisy-kit/signature.css'),
    import('@daisy-kit/transfer-list.css'),
]);

const [signature, transferList] = await Promise.all([
    import('@daisy-kit/signature.js'),
    import('@daisy-kit/transfer-list.js'),
]);

signature.mountAll();
transferList.mountAll();
