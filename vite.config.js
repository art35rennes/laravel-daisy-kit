import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            buildDirectory: 'vendor/art35rennes/laravel-daisy-kit',
            hotFile: 'storage/daisy-kit-vite.hot',
        }),
        tailwindcss(),
    ],
});
