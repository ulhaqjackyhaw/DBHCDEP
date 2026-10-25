import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';

// Konfigurasi Vite untuk Laravel dan React
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'], // Masukkan file CSS dan JS utama Anda
            refresh: true,  // Aktifkan refresh otomatis pada perubahan file
        }),
        react(),  // Plugin untuk React
    ],
});
