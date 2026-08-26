import { defineConfig } from 'vite';
import { resolve } from 'node:path';
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    root: import.meta.dirname,
    plugins: [
        laravel({
            input: {
                'resources/css/app.css': 'resources/css/app.css',
                'resources/js/app.js': 'resources/js/app.js',
            },
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        emptyOutDir: true,
        outDir: 'public/build',
    },
    resolve: {
        alias: {
            '@daisy-kit': resolve(import.meta.dirname, '../dist'),
        },
    },
});
