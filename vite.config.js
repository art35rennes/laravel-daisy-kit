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
                    
                    // Séparer Leaflet et ses plugins (très volumineux)
                    if (id.includes('leaflet-routing-machine')) {
                        return 'leaflet-routing';
                    }
                    if (id.includes('leaflet.draw')) {
                        return 'leaflet-draw';
                    }
                    if (id.includes('leaflet.markercluster')) {
                        return 'leaflet-cluster';
                    }
                    if (id.includes('leaflet-measure')) {
                        return 'leaflet-measure';
                    }
                    if (id.includes('leaflet') && !id.includes('node_modules')) {
                        return 'leaflet-core';
                    }
                    if (id.includes('node_modules') && id.includes('leaflet')) {
                        return 'leaflet-vendor';
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
