import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',   // biar bisa diakses dari luar (ngrok, LAN, dll)
        port: 5173,        // default vite
        hmr: {
            host: 'localhost', // kalau mau stabil, bisa ganti ke subdomain ngrok
        },
    },
});
