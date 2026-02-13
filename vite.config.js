import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/index.js',
                'resources/css/login.css',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('alpinejs')) return 'vendor-alpine';
                        if (id.includes('apexcharts')) return 'vendor-apexcharts';
                        if (id.includes('sweetalert2')) return 'vendor-sweetalert';
                        if (id.includes('flatpickr')) return 'vendor-flatpickr';
                        if (id.includes('jsvectormap')) return 'vendor-jsvectormap';
                        if (id.includes('dropzone')) return 'vendor-dropzone';
                        return 'vendor';
                    }
                },
            },
        },
    },
});