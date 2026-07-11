import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

const resolveChunkName = (id) => {
    // Séparer les gros éditeurs de code
    if (id.includes('codemirror') || id.includes('code-editor')) {
        return 'code-editor';
    }

    // Séparer Trix (éditeur WYSIWYG)
    if (id.includes('trix')) {
        return 'trix';
    }

    // Leaflet: markercluster gets its own chunk (~13KB gz, only loaded when cluster=true).
    if (id.includes('leaflet.markercluster') || id.includes('markercluster')) {
        return 'leaflet-cluster';
    }
    if (id.includes('resources/js/leaflet/plugins/draw')) {
        return 'leaflet-draw';
    }
    if (id.includes('terra-draw')) {
        return 'leaflet-draw';
    }
    if (id.includes('@turf')) {
        return 'leaflet-measure';
    }
    // Leaflet core library + small plugins (gesture, fullscreen) share one vendor chunk.
    if (id.includes('node_modules/leaflet')) {
        return 'leaflet-vendor';
    }
    if (id.includes('leaflet') && !id.includes('node_modules')) {
        return 'leaflet-core';
    }

    // Séparer ECharts et le runtime charts du package
    if (id.includes('echarts') || id.includes('chart/')) {
        return 'chart';
    }

    // Isoler TanStack Table pour eviter de gonfler le bundle principal.
    if (id.includes('@tanstack/table-core')) {
        return 'table-core';
    }

    if (id.includes('jsonata')) {
        return 'jsonata';
    }

    if (id.includes('docx-preview') || id.includes('jszip')) {
        return 'docx-preview';
    }

    if (id.includes('form-kit')) {
        return 'form-kit';
    }

    if (
        id.includes('resources/js/modules/blueprint')
        || id.includes('resources/js/blueprint')
        || id.includes('@dagrejs/dagre')
        || id.includes('@dagrejs/graphlib')
    ) {
        return 'blueprint';
    }

    // Séparer les autres dépendances lourdes
    if (id.includes('node_modules')) {
        // Regrouper les petites dépendances ensemble
        if (id.includes('cally') || id.includes('calendar')) {
            return 'calendar';
        }

        // Toutes les autres dépendances node_modules
        return 'vendor';
    }

    return null;
};

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            publicDirectory: 'dist',
            buildDirectory: 'vendor/art35rennes/laravel-daisy-kit',
            hotFile: '.vite/daisy-kit-vite.hot',
        }),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            output: {
                strictExecutionOrder: true,
                codeSplitting: {
                    includeDependenciesRecursively: false,
                    minSize: 0,
                    groups: [
                        { name: 'blueprint', test: (id) => resolveChunkName(id) === 'blueprint', priority: 60 },
                        { name: 'code-editor', test: (id) => resolveChunkName(id) === 'code-editor', priority: 50 },
                        { name: 'trix', test: (id) => resolveChunkName(id) === 'trix', priority: 50 },
                        { name: 'leaflet-cluster', test: (id) => resolveChunkName(id) === 'leaflet-cluster', priority: 50 },
                        { name: 'leaflet-draw', test: (id) => resolveChunkName(id) === 'leaflet-draw', priority: 50 },
                        { name: 'leaflet-measure', test: (id) => resolveChunkName(id) === 'leaflet-measure', priority: 50 },
                        { name: 'leaflet-vendor', test: (id) => resolveChunkName(id) === 'leaflet-vendor', priority: 50 },
                        { name: 'leaflet-core', test: (id) => resolveChunkName(id) === 'leaflet-core', priority: 50 },
                        { name: 'chart', test: (id) => resolveChunkName(id) === 'chart', priority: 50 },
                        { name: 'table-core', test: (id) => resolveChunkName(id) === 'table-core', priority: 50 },
                        { name: 'jsonata', test: (id) => resolveChunkName(id) === 'jsonata', priority: 50 },
                        { name: 'docx-preview', test: (id) => resolveChunkName(id) === 'docx-preview', priority: 50 },
                        { name: 'form-kit', test: (id) => resolveChunkName(id) === 'form-kit', priority: 50 },
                        { name: 'calendar', test: (id) => resolveChunkName(id) === 'calendar', priority: 40 },
                        { name: 'vendor', test: (id) => resolveChunkName(id) === 'vendor', priority: 10 },
                    ],
                },
            },
        },
        chunkSizeWarningLimit: 1500,
    },
});
