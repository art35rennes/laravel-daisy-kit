import { resolve } from 'node:path';
import { defineConfig } from 'vite';

export default defineConfig({
    build: {
        emptyOutDir: true,
        lib: {
            cssFileName: 'file-preview-frame',
            entry: resolve(import.meta.dirname, 'resources/js/file-preview-frame.js'),
            formats: ['iife'],
            name: 'DaisyKitFilePreviewFrame',
            fileName: () => 'file-preview-frame.js',
        },
        outDir: '.tmp/file-preview-frame',
    },
});
