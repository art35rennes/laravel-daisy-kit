import { resolve } from 'node:path';
import inject from '@rollup/plugin-inject';
import { defineConfig } from 'vite';

const entries = {
    table: resolve(import.meta.dirname, 'resources/js/table.js'),
    tree: resolve(import.meta.dirname, 'resources/js/tree.js'),
    blueprint: resolve(import.meta.dirname, 'resources/js/blueprint.js'),
    'file-preview': resolve(import.meta.dirname, 'resources/js/file-preview.js'),
    map: resolve(import.meta.dirname, 'resources/js/map.js'),
    copyable: resolve(import.meta.dirname, 'resources/js/copyable.js'),
    combobox: resolve(import.meta.dirname, 'resources/js/combobox.js'),
    signature: resolve(import.meta.dirname, 'resources/js/signature.js'),
    truncate: resolve(import.meta.dirname, 'resources/js/truncate.js'),
    scrollspy: resolve(import.meta.dirname, 'resources/js/scrollspy.js'),
    'transfer-list': resolve(import.meta.dirname, 'resources/js/transfer-list.js'),
};

export default defineConfig({
    base: './',
    plugins: [inject({
        include: [
            '**/node_modules/leaflet.markercluster/src/**',
            '**/node_modules/leaflet-gesture-handling/dist/**',
        ],
        L: ['leaflet', 'default'],
    })],
    build: {
        assetsInlineLimit: 0,
        emptyOutDir: true,
        modulePreload: false,
        manifest: true,
        outDir: 'dist',
        rollupOptions: {
            input: entries,
            preserveEntrySignatures: 'strict',
            output: {
                entryFileNames: '[name].js',
                assetFileNames: '[name][extname]',
                chunkFileNames: 'chunks/[name]-[hash].js',
            },
        },
    },
});
