import { resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';

const __dirname = fileURLToPath(new URL('.', import.meta.url));

export default defineConfig({
    build: {
        manifest: true,
        outDir: 'build',
        rollupOptions: {
            input: [
                resolve(__dirname, 'index.html'),
                resolve(__dirname, 'relaxed.html'),
            ],
        },
    },
    resolve: {
        alias: {
            '@daisy-kit': resolve(__dirname, 'vendor/art35rennes/laravel-daisy-kit/dist'),
        },
    },
});
