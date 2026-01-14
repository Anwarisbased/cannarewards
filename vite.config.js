import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            'ziggy-js': resolve('vendor/tightenco/ziggy'),
        },
    },
    server: {
        host: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
