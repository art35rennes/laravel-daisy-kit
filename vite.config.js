import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            buildDirectory: 'vendor/art35rennes/laravel-daisy-kit',
            hotFile: '.vite/daisy-kit-vite.hot',
        }),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: (id) => {
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
                    // Leaflet core library + small plugins (gesture, fullscreen) share one vendor chunk.
                    if (id.includes('node_modules/leaflet')) {
                        return 'leaflet-vendor';
                    }
                    if (id.includes('leaflet') && !id.includes('node_modules')) {
                        return 'leaflet-core';
                    }
                    
                    // Séparer Chart.js
                    if (id.includes('chart.js') || id.includes('chart/')) {
                        return 'chart';
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
                },
            },
        },
        // Augmenter la limite d'avertissement pour les chunks manuels
        chunkSizeWarningLimit: 1000,
    },
});
