import { resolve } from 'node:path';
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
    build: {
        emptyOutDir: true,
        manifest: true,
        outDir: 'dist',
        rollupOptions: {
            input: entries,
            output: {
                entryFileNames: '[name].js',
                assetFileNames: '[name][extname]',
                chunkFileNames: 'chunks/[name]-[hash].js',
            },
        },
    },
});
