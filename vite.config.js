import { resolve } from 'node:path';
import inject from '@rollup/plugin-inject';
import { defineConfig } from 'vite';

const entries = {
    'forms-viewer': resolve(import.meta.dirname, 'resources/js/forms/viewer.js'),
    'forms-builder': resolve(import.meta.dirname, 'resources/js/forms/builder.js'),
    table: resolve(import.meta.dirname, 'resources/js/table.js'),
    tree: resolve(import.meta.dirname, 'resources/js/tree.js'),
    blueprint: resolve(import.meta.dirname, 'resources/js/blueprint.js'),
    'file-preview': resolve(import.meta.dirname, 'resources/js/file-preview.js'),
    map: resolve(import.meta.dirname, 'resources/js/map.js'),
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
