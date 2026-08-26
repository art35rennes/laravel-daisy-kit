import { resolve } from 'node:path';
import { defineConfig } from 'vite';

export default defineConfig({
    build: {
        emptyOutDir: false,
        lib: {
            entry: resolve(import.meta.dirname, 'resources/js/file-preview-frame.js'),
            formats: ['iife'],
            name: 'DaisyKitFilePreviewFrame',
            fileName: () => 'file-preview-frame.js',
        },
        outDir: 'dist',
    },
});
