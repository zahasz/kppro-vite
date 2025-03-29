const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    
    // Włączenie trybu ciemnego
    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Główna paleta kolorów
                'steel-blue': {
                    50: '#f5f7fa',
                    100: '#e4eaf3',
                    200: '#d0dbe9',
                    300: '#b1c2d9',
                    400: '#8fa3c4',
                    500: '#6d86ae',
                    600: '#546e95',
                    700: '#445a7a',
                    800: '#3a4b66',
                    900: '#2c374a',
                    950: '#1e2533',
                },
                // Zastępujemy stare palety kolorów
                primary: {
                    50: '#f1f5fb',
                    100: '#dbe4f2',
                    200: '#bccce7',
                    300: '#93add7',
                    400: '#6585c1',
                    500: '#4668ac',
                    600: '#354f91',
                    700: '#2c4076',
                    800: '#273660',
                    900: '#1f2a49',
                    950: '#131b36',
                },
                accent: {
                    blue: '#3361cc',
                    cyan: '#3894a3',
                    teal: '#358f80',
                    green: '#38a169',
                    amber: '#d69e2e',
                    red: '#e53e3e',
                    purple: '#805ad5',
                    indigo: '#5a67d8',
                },
                // Status colors
                status: {
                    success: {
                        50: '#f0fdf4',
                        500: '#22c55e',
                        700: '#15803d',
                    },
                    warning: {
                        50: '#fffbeb',
                        500: '#f59e0b',
                        700: '#b45309',
                    },
                    danger: {
                        50: '#fef2f2',
                        500: '#ef4444',
                        700: '#b91c1c',
                    },
                    info: {
                        50: '#eff6ff',
                        500: '#3b82f6',
                        700: '#1d4ed8',
                    },
                }
            },
            // Dodajemy specyficzne warianty dla komponentów
            borderRadius: {
                'button': '0.375rem',
                'card': '0.5rem',
            },
            boxShadow: {
                'card': '0 2px 4px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1)',
                'card-hover': '0 4px 8px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06)',
                'dropdown': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                'button': '0 1px 2px rgba(0, 0, 0, 0.05)',
            },
            // Responsywne breakpointy
            screens: {
                'xs': '475px',
                ...defaultTheme.screens,
                '3xl': '1920px',
            },
            // Animacje
            animation: {
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'bounce-slow': 'bounce 2s infinite',
            },
            // Dodajemy specyficzne parametry układu
            spacing: {
                '72': '18rem',
                '80': '20rem',
                '96': '24rem',
                '128': '32rem',
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms')({
            strategy: 'class',
        }),
    ],
};
