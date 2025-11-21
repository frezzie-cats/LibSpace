import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    // ADD THIS RESOLVE BLOCK IF IT IS MISSING
    resolve: {
        alias: {
            // Allows the 'import bootstrap' in app.js to find the package's folder
            '~bootstrap': 'node_modules/bootstrap',
        }
    },
});