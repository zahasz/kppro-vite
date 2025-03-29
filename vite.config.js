import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    build: {
        // Optymalizacja modułów
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs', '@alpinejs/persist', '@alpinejs/focus', '@hotwired/turbo'],
                    admin: ['./resources/js/admin/index.js'],
                },
            },
        },
        // Tworzenie source map dla debugowania
        sourcemap: true,
    },
    // Ustawienie trybu, domyślnie będzie to development
    mode: process.env.NODE_ENV === 'production' ? 'production' : 'development',
    // Konfiguracja serwera deweloperskiego
    server: {
        hmr: {
            // Uruchom HMR z pełnymi odświeżeniami, jeśli zachodzi taka potrzeba
            overlay: true,
        },
    },
});
